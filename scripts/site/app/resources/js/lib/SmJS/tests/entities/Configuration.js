import {describe, it} from "mocha";
import {Sm} from "../Sm"
import {expect} from "chai";

const Configuration = Sm.entities.ConfiguredEntity.Configuration;

class EG_Configuration extends Configuration {
    get inheritables() {
        return ['hello'];
    }
    
    configure_hello() {
        return 'hello';
    }
    
    configure_goodbye() {
        return 'goodbye';
    }
}

describe('Configuration', () => {
    const ConfiguredEntity = Sm.entities.ConfiguredEntity;
    const Std              = Sm.std.Std;
    const EVENTS           = Sm.std.EventEmitter.EVENTS;
    
    it('Can access first and last added configuration', () => {
        const conf_d = new ConfiguredEntity;
        const conf   = new EG_Configuration(conf_d);
        const config = {hello: true};
        
        return conf.configure(config)
                   .then(i => conf.configure({goodbye: 'green'}))
                   .then(i => conf.configure({hello: 'red'}))
                   .then(i => {
                       expect(i.first).to.deep.equal(config);
                       expect(i.current).to.deep.equal({hello: 'red', goodbye: 'green'});
                   });
    });
    
    it('Can create configurations before setting the ConfiguredEntity', done => {
        const conf_d  = new ConfiguredEntity;
        const set_age = 5678093;
        const conf    = Configuration.create({age: set_age});
        conf.establishOwner(conf_d)
            .then((i: Configuration) => {
                const age = i.current.age;
                done(age === set_age ? null : 'Could not set property');
            });
    });
    
    it('Can inherit', () => {
        const conf_d = new ConfiguredEntity;
        const conf   = new EG_Configuration(conf_d);
        
        const config = {hello: true};
        return conf.configure({hello: 'red', goodbye: 'green'})
                   .then(i => {
                       const new_conf = new Configuration(new ConfiguredEntity);
                       return new_conf.configure(i);
                   })
                   .then((i: Configuration) => console.log(i.first));
    });
});