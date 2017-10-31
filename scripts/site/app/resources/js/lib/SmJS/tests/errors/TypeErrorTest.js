import {describe, it} from "mocha";

import {Sm} from "../Sm"

import {expect} from "chai";

describe('TypeError', () => {
    const TypeError = Sm.errors.TypeError;
    it('Can throw an error with a symbol and a time', () => {
        const thro = i => {
            throw new TypeError(null, Symbol('This is a symbol'));
        };
        expect(thro).to.throw(TypeError);
    });
});