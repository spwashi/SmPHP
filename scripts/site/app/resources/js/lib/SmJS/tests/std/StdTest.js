import {expect} from "chai";
import {describe, it} from "mocha";
import {Sm} from "../Sm"

describe('Std', () => {
    const Std         = Sm.std.Std;
    const SymbolStore = Sm.std.symbols.SymbolStore;
    it('Can send and receive events', done => {
        expect(1).to.equal(1);
        const tstStd = Std.init('std_tst_name').initializingObject;
        tstStd.receive('test').then(_ => done());
        tstStd.send('test');
    });
    it('Can send and receive SymbolStore events', done => {
        expect(1).to.equal(1);
        const tstStd               = Std.init('std_name').initializingObject;
        const testEventSymbolStore = tstStd.EVENTS;
        tstStd.receive(testEventSymbolStore).then(_ => done());
        tstStd.send(testEventSymbolStore.item('child'));
    });
    it('Can wait for availability', done => {
        Std.init().initializingObject.available.then(i => done());
    });
    it('Can resolve SymbolStore', () => {
        const std = new Std('id');
        const ss  = std.symbolStore;
        expect(ss).to.be.instanceof(SymbolStore);
    });
    it('Can resolve instances', () => {
        const name = 'boonman';
        let ev     = Std.resolve(name);
        Std.init(name);
        return ev;
    });
    it('Can resolve _properties_', _ => {
        const name = '[Std]test|title';
        
        const std     = Std.init('test').initializingObject;
        const resolve = Std.resolve(name);
        std.registerAttribute('title', {});
        resolve.then(i => _());
    })
});