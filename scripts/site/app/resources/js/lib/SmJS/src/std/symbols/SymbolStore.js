const _to_string = name => {
    const name_type = typeof name;
    let str;
    if (name_type === 'string') return name;
    if (name_type === 'symbol') {
        let slice_name = String(name).slice(7, -1);
        const $_$_str  = '<$_$> ';
        if (slice_name.indexOf($_$_str) > -1) slice_name = '$' + slice_name.slice($_$_str.length) + '$';
        return `<${slice_name}>`
    }
    if (name_type === 'object') return String(name);
    throw new Error(['Cannot handle this type --' + typeof name]);
};

/**
 * Standard SymbolStore
 * @type {Symbol}
 */
const $_$ = Symbol.for('$_$');

/**
 * Class meant to handle a sort of unique ID system using symbols
 * @class SymbolStore
 */
class SymbolStore {
    /**
     * @name SymbolStore.constructor
     * @param name
     * @param parent
     * @param symbol
     */
    constructor(name, parent: SymbolStore = null, symbol = null) {
        let parent_name;
        if (parent instanceof SymbolStore) parent_name = parent.smID;
        
        const _new_name = (parent_name ? _to_string(parent_name || symbol) + ' ' : '') + _to_string(name);
        this._name      = _new_name;
        this._Symbol    = symbol || Symbol(_new_name);
        
        this._items = {};
        /** @type {Set<Symbol>}  */
        this._family = new Set;
        SymbolStore._registry[this._Symbol] = this;
        this._parent                        = null;
        /** @type {SymbolStore}  */
        let originSymbolStore;
        // If we are adding a symbol that exists in the registry, add its family to this one
        if (typeof name === 'symbol' && (originSymbolStore = SymbolStore.find(name))) {
            this._origin = originSymbolStore;
            this._family = new Set([...this.family, ...originSymbolStore.family, originSymbolStore.Symbol])
        }
        if (parent && parent instanceof SymbolStore) {
            this._parent = parent;
            this._family = new Set([...parent.family, parent.Symbol, ...this.family])
        }
    }
    
    /**
     * @return {SymbolStore|boolean}
     */
    static get $_$() {
        return this.find($_$);
    }
    
    get smID() {return this._name}
    
    get origin() {
        return this._origin || this;
    }
    
    get items() {
        return this._items;
    }
    
    /**
     *
     * @return {Set}
     */
    get family() {
        return this._family;
    }
    
    get parent(): ? SymbolStore {
        return this._parent;
    }
    
    get name() {
        return this._name;
    }
    
    get Symbol() {
        return this._Symbol;
    }
    
    /** @return {SymbolStore}*/
    get STATIC() { return this.get_NAME_('STATIC'); }
    
    /** @return {SymbolStore}*/
    get ERROR() { return this.get_NAME_('ERROR'); }
    
    /** @return {SymbolStore}*/
    get TIMEOUT() { return this.get_NAME_('ERROR').item(SymbolStore.$_$.item('TIMEOUT')); }
    
    /** @return {SymbolStore}*/
    get BEGIN() { return this.get_NAME_('BEGIN'); }
    
    /** @return {SymbolStore}*/
    get COMPLETE() { return this.get_NAME_('COMPLETE'); }
    
    /** @return {SymbolStore}*/
    get CANCEL() { return this.get_NAME_('CANCEL'); }
    
    /**
     * @alias  SymbolStore.constructor
     */
    static init(name) {
        return this.find(name) || new SymbolStore(...arguments);
    }
    
    /**
     *
     * @param item
     * @return {SymbolStore|boolean}
     */
    static find(item) {
        if (typeof  item !== 'symbol') return false;
        return this._registry[item] || false;
    }
    
    /**
     *
     * @param {string|SymbolStore|Symbol}   name
     * @return {SymbolStore}
     */
    item(name) {
        const original_name = name;
        if (name instanceof SymbolStore) name = name.Symbol;
        if (this._items[name]) return this._items[name];
        const symbolStore = new SymbolStore(name, this);
        this._items[name] = symbolStore;
        return symbolStore;
    }
    
    get_NAME_(name) {
        if (this.Symbol === $_$) return this.item(name);
        return this.item(SymbolStore.find($_$).item(name))
    }
    
}

SymbolStore._registry = {};

new SymbolStore($_$, null, $_$);

export default SymbolStore;
export {SymbolStore};