/**
 * Created by Sam Washington on 5/20/17.
 */
import {mapToObj} from "../util/index";
import {Std} from "../std/";
import Configuration from "./Configuration";

/**
 * @name Sm.entities.ConfiguredEntity
 */
export default class ConfiguredEntity extends Std {
    static Configuration: typeof Configuration = Configuration;
    static smID                                = 'ConfiguredEntity';
    
    constructor(name, config: { _id: string } = {}) {
        if (typeof name === "object" && name) {
            config = name;
            name   = null;
        }
        
        super(name);
        this._parentSymbols = new Set;
        this._parents       = new Set;
        config._id          = config._id || name;
    }
    
    get parentSymbols(): Set<Symbol> { return this._parentSymbols; }
    
    /**
     * Get an array of the Fields we're going to encode in JSON
     * @return {Set<string>}
     */
    get jsonFields() {
        return new Set(['smID', '?inherits']);
    }
    
    get configuration(): Configuration {
        return this.getConfiguration();
    }
    
    static factory() {
        const c_tor = this;
        return (new c_tor())
    }
    
    initialize(config): Promise<ConfiguredEntity> {
        let inherits                     = config.inherits;
        const completeInitialInheritance = this._completeInitialInheritance(inherits);
        return super.initialize(config)
                    .then(i => completeInitialInheritance)
                    .then(i => this.configure(config))
                    .then(i => this)
    }
    
    toJSON__inherits() {
        const inherits = new Set;
        this._parents.forEach((item: ConfiguredEntity) => inherits.add(item.smID));
        return [...inherits];
    }
    
    toJSON() {
        const jsonFields = this.jsonFields;
        const json_obj   = {};
        jsonFields.forEach(fieldName => {
            let is_optional = fieldName[0] === '?';
            if (fieldName[0] === '?') fieldName = fieldName.substr(1);
            
            const fn_name = `toJSON__${fieldName}`;
            let item      = this[fn_name] ? (this[fn_name]()) : this[fieldName];
            if (item instanceof Map) item = mapToObj(item);
            
            if (is_optional && item instanceof Array && !item.length) return;
            
            json_obj[fieldName] = item;
        });
        return json_obj;
    }
    
    /**
     * Set the properties of this object using another object.
     *
     * @param properties
     * @return {Promise.<*>}
     */
    configure(properties): Promise<ConfiguredEntity> {
        const CONFIGURE = this.EVENTS.item('configure');
        return this.send(CONFIGURE.BEGIN)
                   .then(i => this.getConfiguration().configure(properties))
                   .then(i => this.send(CONFIGURE.COMPLETE));
    }
    
    getConfiguration(): Configuration {
        const c_Helper = this.constructor.Configuration || Configuration;
        
        this._configuration = this._configuration || new c_Helper(this);
        return this._configuration;
    }
    
    /**
     *
     * @param item
     * @return {*|Promise<[]>}
     */
    inherit(item) {
        if (!item) return Promise.resolve([]);
        
        let constructor = this.constructor;
        
        const INHERITANCE      = Std.EVENTS.item('inheritance');
        const self_INHERITANCE = this.EVENTS.item(INHERITANCE);
        
        return constructor
            .resolve(item)
            
            // Send the BEGIN event
            .then((result: [Event, ConfiguredEntity]) => {
                const parent: ConfiguredEntity = result[1] || null;
                
                console.log('-- config --', this.configuration.current);
                
                if (!parent) {
                    // console.error();
                    return false;
                }
                const INHERIT_EVENT           = self_INHERITANCE.item(parent.symbolStore);
                const BEGIN_INHERITANCE_EVENT = INHERIT_EVENT.BEGIN.STATIC;
                
                return this.send(BEGIN_INHERITANCE_EVENT, parent).then(item => parent);
            })
            
            .catch(e => {
                throw e;
            })
            
            // Actually inherit the entity
            .then((parent: ConfiguredEntity) => {
                if (!(parent instanceof this.constructor) && !(typeof parent === 'function' && (this instanceof parent.constructor))) {
                    let c = this.constructor;
                    // We can only inherit from things that are part of this family.
                    throw new Error('Cannot accept ' + (String(parent)));
                }
                
                // Say that we've inherited from this item
                this._parentSymbols.add(parent.Symbol);
                this._parents.add(parent);
                return this.configure(parent).then(i => parent)
            })
            
            // Send a closing Event
            .then((parent: ConfiguredEntity) => {
                const INHERIT_EVENT              = self_INHERITANCE.item(parent.symbolStore);
                const COMPLETE_INHERITANCE_EVENT = INHERIT_EVENT.COMPLETE.STATIC;
                this.send(COMPLETE_INHERITANCE_EVENT, item)
            });
    }
    
    /**
     * Inherit from all of the parent identifiers we said we want to inherit from in the original configuration
     * @param parent_identifiers
     * @return {Promise<Std>}
     * @private
     */
    _completeInitialInheritance(parent_identifiers) {
        parent_identifiers     = Array.isArray(parent_identifiers) ? parent_identifiers : [parent_identifiers];
        const INHERIT          = Std.EVENTS.item('inheritance').item('configuration');
        const inheritedFollows = [];
        parent_identifiers.forEach((item: ConfiguredEntity) => {
            const pId = !!item ? this.inherit(item) : null;
            inheritedFollows.push(pId);
        });
        return this.send(this.EVENTS.item(INHERIT.BEGIN).STATIC, this)
                   .then(i => Promise.all(inheritedFollows))
                   .then(i => this.send(this.EVENTS.item(INHERIT.COMPLETE).STATIC, this))
    }
}