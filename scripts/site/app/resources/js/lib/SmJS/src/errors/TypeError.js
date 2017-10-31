import {StdError} from "./Error";

export default class TypeError extends StdError {
    constructor(message, symbol) {
        message = message || 'Incorrect type ';
        super(message, symbol);
    }
}