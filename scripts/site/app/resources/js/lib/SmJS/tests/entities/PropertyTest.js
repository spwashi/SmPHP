import {describe, it} from "mocha";
import {Sm} from "../Sm"

import {expect} from "chai";

describe('Property', () => {
    const Property     = Sm.entities.Property;
    const DataSource   = Sm.entities.DataSource;
    const Datatype     = Sm.entities.Datatype;
    const testProperty = Property.init('testProperty').initializingObject;
    it('exists', () => {
        expect(testProperty.Symbol).to.be.a('symbol');
        expect(testProperty.Symbol.toString()).to.equal(Symbol(`[${Property.name}]testProperty`).toString())
    });
    it('Can configure DataSource', () => {
        const pn  = 'P_ccd_pn';
        const dsn = 'P_ccd_dsn';
        DataSource.init(dsn, {type: 'database'});
        return Property.init(pn, {source: dsn})
                       .then(/**@param Property*/property => {
                           const dataSource = property.dataSource;
                           if (!(dataSource instanceof DataSource)) throw new Error("Could not resolve dataSource properly");
                       });
    });
    it('Can inherit from other Properties', () => {
        const parent_pn = 'cifp_parent_pn', child_pn = 'cifp_child_pn';
        let parent      = Property.init(parent_pn, {}).initializingObject;
        return Property.init(child_pn, {inherits: [parent_pn]})
                       .then((property: Property) => {
                           expect([...property.parentSymbols]).to.contain((parent.Symbol));
                       });
    });
    it('Can inherit Datatypes from other Properties', () => {
        const parent_pn       = 'cidfp_parent_pn', child_pn = 'cidfp_child_pn';
        const parentPromise   = Property.init(parent_pn, {datatypes: ['int']});
        const datatypePromise = Datatype.init('int');
        const childPromise    = Property.init(child_pn, {inherits: [parent_pn]});
        return Promise.resolve(parentPromise)
                      .then(i => datatypePromise)
                      .then(i => childPromise)
                      .then(/** @param {Property}  */property => {
                          expect([...property.parentSymbols]).to.contain((parentPromise.initializingObject.Symbol));
                          expect([...property.datatypes]).to.contain(datatypePromise.initializingObject)
                      });
    });
    it('Can be JSON', () => {
        Datatype.init('int');
        Datatype.init('string');
        return Property.init('P_cbj_cen', {
                           datatypes: ['int', 'string'],
                           length:    10
                       })
                       .then(model => {
                           const stringify = JSON.stringify(model);
                           const parse     = JSON.parse(stringify);
                           expect(parse).to.haveOwnProperty('smID');
                           expect(parse).to.haveOwnProperty('datatypes');
                           expect(parse).to.haveOwnProperty('length');
                           expect(parse.datatypes.length).to.equal(2);
                       });
    })
});