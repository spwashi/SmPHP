/**
 * Created by Sam Washington on 5/20/17.
 */

import {default as EventEmitter, EVENTS} from "./EventEmitter";
import SymbolStore from "./symbols/SymbolStore";
import TimeoutError from "../errors/TimeoutError";
import Sm from "../index";

const ATTRIBUTE = SymbolStore.$_$.item('_attribute_').Symbol;

/**
 * @class Sm.std.Std
 */
class Std {
    static smID = 'Std';
    
    /**
     * @param identifier This is some sort of identifier for this object
     */
    constructor(identifier) {
        /** @type {events.EventEmitter}  */
        this._Events = new EventEmitter(this);
        this._originalName = identifier;
        
        //region Status
        this._isAvailable = false;
        this._isComplete  = false;
        //endregion
        
        // Make sure Sm is aware of this SmEntity
        Std.registerSmEntity(this.constructor.smID, this.constructor);
        
        this.smID = this.constructor.createName(identifier);
        if (typeof identifier !== 'symbol') identifier = Symbol.for(this.smID);
        this._Symbol      = identifier;
        const symbolStore = this.constructor.getSymbolStore(identifier);
        /**
         * @type {SymbolStore}
         * @protected
         */
        this._symbolStore = symbolStore;
        /**
         * Register Attributes as a Map
         * @type {Map}
         */
        this._attributes = new Map;
        /**
         * Refers to the identifiers of the events emitted by this class
         * @type {SymbolStore}
         */
        this[EVENTS] = symbolStore.item(EVENTS);
        /** @type {SymbolStore} The Event that marks the beginning of this object's initialization */
        const BEGIN = Std.EVENTS.item('init').BEGIN;
        this.send(this.EVENTS.item(BEGIN).STATIC, this);
    }
    
    /**
     * @return {EventEmitter|*|events.EventEmitter}
     */
    static get Events() { return this._Events || (this._Events = new EventEmitter(this)) }
    
    /**
     * @return {SymbolStore}
     */
    static get EVENTS() { return this[EVENTS] || (this[EVENTS] = new SymbolStore(Symbol.for(this.smID))); }
    
    get symbolStore() {
        return this._symbolStore;
    }
    
    /**
     * Get this object when it is available.
     *
     * @return {this}
     */
    get available() {
        return this.receive(this.EVENTS.item(Std.EVENTS.item('available'))).then(i => i[1] || null);
    }
    
    get isAvailable() {return this._isAvailable}
    
    get isComplete() {return this._isComplete}
    
    /**
     * Get the Symbol that identifies this object
     * @return {Symbol}
     * @constructor
     */
    get Symbol() { return this._Symbol; }
    
    get symbolName() {return this._Symbol.toString();}
    
    get originalName() {return this._originalName}
    
    /**
     * @return {events.EventEmitter}
     * @constructor
     */
    get Events() { return this._Events; }
    
    /**
     * @return {SymbolStore}
     */
    get EVENTS() { return this[EVENTS]; }
    
    //
    static createName(name): string {
        name = name || Math.random().toString(36).substr(4, 6);
        return `[${this.smID}]${name}`
    }
    
    /**
     *
     * @param {string|symbol}   identifier
     * @param {{_id?:string}}              config
     * @return {Promise<Std>}
     */
    static init(identifier, config = {}) {
        if (typeof identifier === "object" && identifier) {
            config     = identifier;
            identifier = null;
        }
        const self                 = new this(...arguments);
        config._id                 = config._id || identifier;
        const promise              = self
            .initialize(config)
            .then(self => self._sendInitComplete(this.smID));
        promise.initializingObject = self;
        return promise;
    }
    
    /**
     * @param symbol
     * @return {SymbolStore}
     */
    static getSymbolStore(symbol) {
        /** @type {string} item If we are retrieving a property of this item */
        
        
        let prop; // This is set if we are trying to retrieve a property of the thing we're talking about. e.g. )       [Entity]sam|name
        
        if (typeof symbol === 'string') {
            let identifer = this === Std ? '[' : `[${this.smID}]`;
            
            // Pipes identify properties
            if (symbol.indexOf('|') > 0) {
                [symbol, prop] = symbol.split('|') || null;
            }
            if (symbol.indexOf(identifer) !== 0) {
                symbol = this.createName(symbol);
            }
            symbol = Symbol.for(symbol);
            
        }
        
        // Create a symbol store based on the symbol
        const symbolStore = SymbolStore.init(symbol, null, symbol);
        
        // If we aren't returning a property, return that symbol store
        if (!prop) {
            return symbolStore;
        }
        
        // return from the
        return symbolStore.item(ATTRIBUTE).item(prop);
    }
    
    static resolve(symbol) {
        let symbolStore = this.getSymbolStore(symbol);
        
        // If we are trying to resolve something that has been registered as an attribute
        const is_property = symbolStore.family.has(ATTRIBUTE);
        const COMPLETE    = is_property ? symbolStore : symbolStore.item(EVENTS)
                                                                   .item(Std.EVENTS.item('init').COMPLETE);
        return Std.receive(COMPLETE)
    }
    
    static available(symbol) {
        let symbolStore = this.getSymbolStore(symbol);
        
        // If we are trying to resolve something that has been registered as an attribute
        const is_property = symbolStore.family.has(ATTRIBUTE);
        const COMPLETE    = is_property ? symbolStore : symbolStore.item(EVENTS)
                                                                   .item(Std.EVENTS.item('available'));
        return Std.receive(COMPLETE)
    }
    
    static send(eventName, ...args) {
        this.Events.emit(eventName, ...args);
        return Promise.resolve(this)
    }
    
    /**
     *
     * @param self
     * @param eventName
     * @param callback
     * @param once
     * @return {Promise}
     * @private
     */
    static _receive(self: Sm.std.Std | typeof Sm.std.Std, eventName, callback, once = true) {
        let _promiseEssence: promiseEssence = {
            resolve: null,
            reject:  null
        };
        
        // Initialize the resolve and reject promises to return from this method
        const promise = new Promise((yes, no) => {
            [_promiseEssence.resolve, _promiseEssence.reject] = [yes, no];
        });
        
        self._waitForEvent(callback, _promiseEssence, self, eventName, once);
        
        return promise;
    };
    
    /**
     * @name Sm.std.Std._waitForEvent
     * @param callback
     * @param _promiseEssence
     * @param self
     * @param eventName
     * @param once
     * @private
     */
    static _waitForEvent(callback, _promiseEssence, self, eventName, once) {
        
        // resolve
        let resolve = (...args) => {
            // Run the callback if it exists
            if (typeof callback === 'function') callback(...args);
            
            // Resolve with whatever we would
            return _promiseEssence.resolve(args);
        };
        
        // reject
        const granted_time = 500;
        const timeoutError = new TimeoutError('Timeout in ' + (self.symbolName || self.smID), eventName, granted_time);
        setTimeout(i => {
            return _promiseEssence.resolve(timeoutError)
        }, granted_time);
        
        if (once) {
            self.Events.once(eventName, resolve);
        } else {
            self.Events.on(eventName, resolve);
        }
    }
    
    static receive(eventName, fn, once = true) {
        return this._receive(this, ...arguments);
    }
    
    static registerSmEntity(smID, smEntity) {
        this._smEntities = this._smEntities || {};
        if (this._smEntities[smID]) return this;
        
        this.send(Std.EVENTS.item(`[${smID}]`).STATIC, smEntity);
        this._smEntities[smID] = smEntity;
        
        return this;
    }
    
    static getSmEntity(smID): Std {
        return (this._smEntities || {})[smID];
    }
    
    /**
     * @alias Sm.std.Std._waitForEvent
     * @param args
     * @private
     */
    _waitForEvent(...args) {
        return this.constructor._waitForEvent(...args);
    }
    
    receive(eventName, fn, once = true) {
        return this.constructor._receive(this, ...arguments);
    }
    
    registerAttribute(name, attribute) {
        const propertySymbolStore = this._symbolStore.item(ATTRIBUTE).item(name);
        this._attributes.set(name, attribute);
        return this.send(propertySymbolStore.STATIC, attribute);
    }
    
    send(eventName, ...args) {
        this._Events.emit(eventName, ...args);
        this.constructor.send(eventName, ...args);
        if (this.constructor !== Std) {
            Std.send(eventName, ...args);
        }
        return Promise.resolve(this);
    }
    
    /**
     * Resolve an attribute
     * @param symbol
     * @return {*}
     */
    resolve(symbol) {
        return Std.receive(this._symbolStore.item(ATTRIBUTE).item(symbol));
    }
    
    initialize(config): Promise<Sm.std.Std> {
        return Promise.resolve(this);
    }
    
    /**
     * Emit an event saying that we are done initializing this object
     * @param {string}name Only if the name passed in matches the currently active class will we mark this class as complete
     * @return {Promise}
     */
    _sendInitComplete(name) {
        if (name === this.constructor.smID) {
            return this._sendAvailable(name)
                       .then(i => {
                           this._isComplete = true;
                           this.send(this.EVENTS.item(Std.EVENTS.item('init').COMPLETE).STATIC, this);
                           return this;
                       });
        }
        return Promise.resolve(null);
    }
    
    /**
     * Emit an event saying that this object is complete enough to be available
     *
     * @param {string} name The name of the class calling this function. Only the current class should call this function effectively
     * @private
     */
    _sendAvailable(name) {
        if (name === this.constructor.smID && !this._isAvailable) {
            this._isAvailable = true;
            return this.send(this.EVENTS.item(Std.EVENTS.item('available')).STATIC, this);
        }
        return Promise.resolve(null);
    }
}

export default Std;
export {Std};