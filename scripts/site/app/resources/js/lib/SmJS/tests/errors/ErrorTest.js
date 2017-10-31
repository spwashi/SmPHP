import {describe, it} from "mocha";
import {Sm} from "../Sm"
import {expect} from "chai";

describe('Errors', () => {
    const GenericError = Sm.errors.GenericError;
    it('Can throw an error with a symbol', () => {
        const thro = i => {
            try {
                throw new GenericError("This is a test", Symbol('This is a symbol'));
            } catch (e) {
                throw e;
            }
        };
        expect(thro).to.throw(GenericError);
    });
});