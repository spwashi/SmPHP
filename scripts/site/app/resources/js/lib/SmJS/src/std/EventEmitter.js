import events from "events";
import {SymbolStore} from "./symbols/SymbolStore";

const EVENTS = Symbol('EVENTS');
export {EVENTS};

export class Event {
    constructor(emitter, eventName, activeSymbol, eventFamily, args) {
        this._emitter      = emitter;
        this._eventName    = eventName;
        this._activeSymbol = activeSymbol;
        this._eventFamily  = eventFamily;
        this._args         = args;
    }
    
    get args() { return this._args; }
    
    /** @return SymbolStore*/
    get eventName() {return this._eventName;}
    
    /** @return {Symbol} */
    get activeSymbol() {return this._activeSymbol;}
    
    get eventFamily() {return this._eventFamily;}
    
    get emitter() {return this._emitter;}
}

/**
 * @class EventEmitter
 * @extends events.EventEmitter
 */
export default class EventEmitter extends events.EventEmitter {
    constructor(emitter) {
        super();
        this._emitter = emitter || null;
        
        this._emittedEvents = new Map;
    }
    
    on(event_name, fn) {
        if (event_name instanceof SymbolStore) event_name = event_name.Symbol;
        if (this._emittedEvents.has(event_name)) {
            if (typeof fn === 'function') fn(...this._emittedEvents.get(event_name));
            return this;
        }
        else return super.on(event_name, fn);
    }
    
    /**
     * Emit an event
     *
     * @param {string|Symbol|SymbolStore} event_name The event identifier that we are emitting
     * @param event
     * @param args
     */
    emit(event_name, event, ...args) {
        let family             = new Set;
        let _originalEventName = event_name;
        
        if (_originalEventName instanceof SymbolStore) {
            family     = _originalEventName.family;
            event_name = event_name.Symbol;
        }
        
        if (!(event instanceof Event)) {
            args.splice(0, 0, event);
            event = new Event(this._emitter || null,
                _originalEventName,
                event_name,
                family,
                args);
        }
        args.splice(0, 0, event);
        
        if (_originalEventName instanceof SymbolStore && _originalEventName.origin === SymbolStore.$_$.STATIC) {
            const eventSymbolStore = _originalEventName.parent;
            this._emittedEvents.set(eventSymbolStore.Symbol, [...args]);
        }
        
        family.forEach(symbol => this.emit(symbol, ...args));
        super.emit(event_name, ...args);
    }
}
EventEmitter.EVENTS = EVENTS;