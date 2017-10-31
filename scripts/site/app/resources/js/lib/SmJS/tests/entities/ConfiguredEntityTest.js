import {describe, it} from "mocha";
import {Sm} from "../Sm"
import {expect} from "chai";

describe('ConfiguredEntity', () => {
    const ConfiguredEntity = Sm.entities.ConfiguredEntity;
    const Std              = Sm.std.Std;
    const EVENTS           = Sm.std.EventEmitter.EVENTS;
    it('exists', () => {
        return ConfiguredEntity.init('test')
                               .then(configuredEntity => {
                                   expect(configuredEntity.Symbol).to.be.a('symbol');
                                   expect(configuredEntity.Symbol.toString()).to.equal(Symbol(`[${ConfiguredEntity.name}]test`).toString())
                               });
    });
    
    it('Can resolve', d => {
        ConfiguredEntity.resolve('one').then(i => d());
        ConfiguredEntity.init('one');
        
    });
    
    it('init.BEGIN event', d => {
        const BEGIN = Std.EVENTS.item('init').BEGIN;
        ConfiguredEntity.receive(BEGIN).then(_ => {d()});
        ConfiguredEntity.init('name');
    });
    
    it('init.COMPLETE event', d => {
        const BEGIN    = Std.EVENTS.item('init').BEGIN;
        const COMPLETE = Std.EVENTS.item('init').COMPLETE;
        
        let begin_called = false,
            d_called     = false;
        
        function fn() {
            if (d_called) return;
            d_called = true;
            if (begin_called) d();
            else d('Wrong order of events');
        }
        
        ConfiguredEntity.receive(BEGIN).then(_ => fn(begin_called = true));
        ConfiguredEntity.receive(COMPLETE).then(fn);
        ConfiguredEntity.init('name');
    });
    
    it('Can inherit', d => {
        const initTestParent = ConfiguredEntity.init('parent');
        const child          = ConfiguredEntity.getSymbolStore('child').item(EVENTS);
        
        const INHERIT  = child.item(Std.EVENTS.item('inherit').COMPLETE);
        const COMPLETE = child.item(Std.EVENTS.item('init').COMPLETE);
        
        let begin_called = false;
        initTestParent.then(testParent => {
            const waitForInherit = ConfiguredEntity.receive(INHERIT)
                                                   .then(_ => begin_called = true);
            const waitForComplete = ConfiguredEntity.receive(COMPLETE)
                                                    .then(i => {
                                                        const testChild = i[1];
                                                        let error_message;
                
                                                        if (!(testChild instanceof ConfiguredEntity)) error_message = ['Improper child', testChild];
                                                        else if (!testChild.parentSymbols.has(testParent.Symbol)) error_message = 'COuld not inherit';
                
                                                        d(error_message)
                                                    });
            
            // Create the inheriting child
            ConfiguredEntity.init('child', {inherits: 'parent'});
            
            return waitForComplete;
        });
        
    });
    
    it('Can be JSON', () => {
        return ConfiguredEntity.init('CE_cbj_cen', {})
                               .then(configuredEntity => {
                                   expect(JSON.parse(JSON.stringify(configuredEntity))).to.haveOwnProperty('smID')
                               })
    })
});