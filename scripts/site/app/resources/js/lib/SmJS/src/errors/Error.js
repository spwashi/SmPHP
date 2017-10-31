/**
 * @param sup
 * @constructor
 * @extends Error
 */
import SymbolStore from "../std/symbols/SymbolStore";

export class StdError extends Error {
    constructor(message, symbol) {
        
        let symbolString;
        
        if (symbol instanceof SymbolStore) {
            symbolString = symbol.smID;
        } else if (typeof symbol === "symbol") {
            symbolString = symbol.toString();
        } else {
            symbolString = symbol;
        }
        
        if (typeof message === 'string' && typeof symbolString === 'string') {
            message += ` (acting on Sym[ ${symbolString} ])`;
        }
        
        super(message);
        
        this.activeSymbol = symbol;
        this.smID         = this.constructor.smID;
        
        this._addToStack(message);
    }
    
    _addToStack(message) {
        if (typeof Error.captureStackTrace === "function") {
            Error.captureStackTrace(this, this.constructor);
        } else {
            this.stack = (new Error(message).stack);
        }
    }
}

export default StdError;