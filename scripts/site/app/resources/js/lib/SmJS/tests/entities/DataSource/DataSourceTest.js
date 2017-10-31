import {describe, it} from "mocha";
import {expect} from "chai";
import {Sm} from "../../Sm"

require('chai-as-promised');

describe('DataSource', () => {
    const DataSource = Sm.entities.DataSource;
    it('exists', () => {
        return DataSource.init('testSource')
                         .then(testSource => {
                             expect(testSource.Symbol).to.be.a('symbol');
                             expect(testSource.Symbol.toString()).to.equal(Symbol(`[${DataSource.name}]testSource`).toString())
                         });
    });
    it('Can be JSON', () => {
        return DataSource.init('DS_cbj_cen', {})
                         .then(model => {
                             const stringify = JSON.stringify(model);
                             const parse     = JSON.parse(stringify);
                             expect(parse).to.haveOwnProperty('smID');
                         });
    })
});