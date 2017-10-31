import {StdError} from "./Error";

export default class TimeoutError extends StdError {
    constructor(message, symbol, granted_time, unit) {
        message = message || 'Timeout ';
        unit    = unit || 'ms';
        if (granted_time && typeof message === 'string') {
            message += ` (${granted_time} ${unit})`;
        }
        super(message, symbol);
    }
}