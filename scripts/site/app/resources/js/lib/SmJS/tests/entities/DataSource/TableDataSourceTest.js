import {describe, it} from "mocha";
import {expect} from "chai";
import {Sm} from "../../Sm"

require('chai-as-promised');

describe('TableDataSource', () => {
    const TableDataSource = Sm.entities.TableDataSource;
    const DataSource      = Sm.entities.DataSource;
    it('exists', () => {
        return TableDataSource.init('testSource')
                              .then(testSource => {
                                  expect(testSource.Symbol).to.be.a('symbol');
                                  expect(testSource.Symbol.toString()).to
                                                                      .equal(Symbol(`[${DataSource.name}]testSource`).toString())
                              });
    });
    it('comes from the factory', done => {
        DataSource.factory('table').then(target => {
            expect(target).to.be.instanceOf(TableDataSource);
            done();
        });
    });
    
    it('Can be JSON', () => {
        return TableDataSource.init('DS_cbj_cen', {})
                              .then(model => {
                                  const stringify = JSON.stringify(model);
                                  const parse     = JSON.parse(stringify);
                                  expect(parse).to.haveOwnProperty('smID');
                              });
    })
});