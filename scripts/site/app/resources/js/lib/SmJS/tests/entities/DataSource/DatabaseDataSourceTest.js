import {describe, it} from "mocha";
import {expect} from "chai";
import {Sm} from "../../Sm"

require('chai-as-promised');

describe('DatabaseDataSource', () => {
    const DatabaseDataSource = Sm.entities.DatabaseDataSource;
    const DataSource         = Sm.entities.DataSource;
    it('exists', () => {
        return DatabaseDataSource.init('testSource')
                                 .then(testSource => {
                                     expect(testSource.Symbol).to.be.a('symbol');
                                     expect(testSource.Symbol.toString()).to
                                                                         .equal(Symbol(`[${DataSource.name}]testSource`).toString())
                                 });
    });
    it('comes from the factory', done => {
        DataSource.factory('database').then(target => {
            expect(target).to.be.instanceOf(DatabaseDataSource);
            done();
        });
    });
    it('Can be JSON', () => {
        return DatabaseDataSource.init('DS_cbj_cen', {})
                                 .then(model => {
                                     const stringify = JSON.stringify(model);
                                     const parse     = JSON.parse(stringify);
                                     expect(parse).to.haveOwnProperty('smID');
                                 });
    })
});