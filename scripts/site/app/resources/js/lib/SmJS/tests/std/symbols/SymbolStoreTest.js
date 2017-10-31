import {expect} from "chai";
import {describe, it} from "mocha";
import {Sm} from "../../Sm"

describe('SymbolStores', () => {
    const SymbolStore     = Sm.std.symbols.SymbolStore;
    const testSymbolStore = new SymbolStore('testSymbolStore');
    it('Has the right name', () => {
        expect(testSymbolStore.name).to.equal('testSymbolStore');
    });
    it('Has the right Symbol', () => {
        expect(typeof testSymbolStore.Symbol).to.equal('symbol');
    });
    it('Can Be Identified', () => {
        expect(SymbolStore.find(testSymbolStore.Symbol)).to.equal(testSymbolStore);
    });
    it('Can use Symbols as Keys', () => {
        const _test_symbol_ = Symbol('another_test');
        expect(testSymbolStore.item(_test_symbol_)).to.be.instanceof(SymbolStore);
    });

//test family
    
    const name = testSymbolStore.item('name');
    expect(name).to.be.instanceOf(SymbolStore);
    expect(name).to.equal(testSymbolStore.item('name'));
    expect(name).to.not.equal(testSymbolStore.item('not a name'));
    it('Can recall parent', () => {
        expect(name.parent).to.equal(testSymbolStore);
    });
    it('Can recall family', () => {
        const child  = name.item('child');
        const family = [...child.family];
        expect(family).to.include(testSymbolStore.Symbol);
        expect(family).to.include(name.Symbol);
    });
    it('Can Inherit Family', () => {
        const testSymbolStore_2  = SymbolStore.init('testSymbolStore_2');
        const CONST_INDEX        = testSymbolStore_2.item('_parent').item('CONST');
        //
        const child_CONST_family = [...testSymbolStore.item('name').item('child').item(CONST_INDEX.Symbol).family];
        expect(child_CONST_family).to.include(testSymbolStore.item('name').Symbol);
        expect(child_CONST_family).to.include(testSymbolStore.item('name').item('child').Symbol);
        expect(child_CONST_family).to.include(testSymbolStore_2.Symbol);
        expect(child_CONST_family).to.include(testSymbolStore_2.item('_parent').Symbol);
    });
});