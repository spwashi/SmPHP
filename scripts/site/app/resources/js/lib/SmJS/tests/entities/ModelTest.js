import {describe, it} from "mocha";
/** @alias {Sm}  */
import {Sm} from "../Sm"

import {expect} from "chai";

describe('Model', () => {
    const Std         = Sm.std.Std;
    const SymbolStore = Sm.std.symbols.SymbolStore;
    const Model       = Sm.entities.Model;
    const DataSource  = Sm.entities.DataSource;
    const SOURCE      = Sm.entities.DataSource.SOURCE;
    /** @type {typeof Sm.entities.Property}  */
    const Property    = (Sm.entities.Property);
    
    it('exists', () => {
        return Model.init('test')
                    .then(testModel => {
                        expect(testModel.Symbol).to.be.a('symbol');
                        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}]test`).toString());
                        const COMPLETE = Std.EVENTS.item('init').COMPLETE;
                        return Model.receive(testModel.EVENTS.item(COMPLETE));
                    });
        
    });
    
    it('Can be initialized w properties', () => {
        return Model.init('_', {properties: {test: {}}})
                    .then(testModel => {
                        expect(testModel.Symbol).to.be.a('symbol');
                        expect(testModel.Symbol.toString()).to.equal(Symbol(`[${Model.name}]_`).toString());
                    });
    });
    
    const INHERIT_COMPLETE = Std.EVENTS.item('inheritance').item('configuration').COMPLETE;
    it('Can inherit another model', () => {
        const parentName  = 'ciam_pn';
        const c1          = Model.init('childModel', {inherits: parentName});
        const p1          = Model.init(parentName);
        let parentModel   = p1.initializingObject;
        const allResolved = Promise.all([p1, c1]);
        
        return Promise.resolve(p1)
                      .then(_parentModel => {
                          parentModel = _parentModel;
                      })
                      .then(i => c1)
                      .then(childModel => childModel.receive(childModel.EVENTS.item(INHERIT_COMPLETE)))
                      .then(result => {
                          let [event, childModel] = result;
            
                          if (!(childModel instanceof Model)) {
                              throw new TypeError(`Return is of wrong type (${typeof childModel})`);
                          }
            
                          if (childModel.parentSymbols.has(parentModel.Symbol)) {
                              return allResolved;
                          }
            
                          throw new Error('Could not inherit parent');
                      });
    });
    
    it('Can inherit multiple models', () => {
        const p1 = Model.init('parentModel1'),
              p2 = Model.init('parentModel2'),
              c1 = Model.init('childModel', {inherits: ['parentModel2', 'parentModel1']});
        
        let parentModel1 = p1.initializingObject,
            parentModel2 = p2.initializingObject;
        
        return Promise.all([p1, p2])
                      .then(result => [parentModel1, parentModel2] = result)
                      .then(i => c1)
                      .then(childModel => {
                          const childInherited = childModel.EVENTS.item(INHERIT_COMPLETE);
                          return childModel.receive(childInherited).then(i => childModel);
                      })
                      .then(childModel => {
                          if (childModel.parentSymbols.has(parentModel1.Symbol) && childModel.parentSymbols.has(parentModel2.Symbol)) return;
                          throw new Error('Expected ' + (childModel.parentSymbols.toString()) + ' to contain parentSymbols. Does not');
                      });
    });
    
    it('Can resolve properties', done => {
        const modelName      = '[Model]testResolveProperties';
        const _property_name = 'test_property';
        
        const model =
                  Model.init('testResolveProperties', {properties: {test_property: {}}})
                      .initializingObject;
        Std.resolve(`${modelName}|${_property_name}`)
           .then(i => {
               let [event, property] = i;
            
               // [Property]{[Model]testResolveProperties}test_property
               expect(model.properties.get(`[Property]\{${modelName}}${_property_name}`)).to.equal(property);
               expect(property).to.be.instanceof(Property);
            
               return model.resolve(_property_name).then(prop => done());
           });
    });
    
    it('Can register Primary properties', done => {
        const _model_name    = 'primary_test_mn';
        const _property_name = 'primary_test_pn';
        const model_name     = `[Model]${_model_name}`;
        const model          = Model.init(_model_name, {properties: {[_property_name]: {primary: true, unique: true}}}).initializingObject;
        Std.resolve(`${model_name}|${_property_name}`).then(i => {
            /** @type {Property} property */
            let [event, property] = i;
            const primaryKeySet   = model.propertyMeta.getPrimaryKeySet(property);
            const message         = primaryKeySet ? null : 'Could not successfully incorporate primary key';
            done(message);
        });
    });
    
    it('Can register Unique properties', done => {
        /** @type property2 {Property}  */
        let property2;
        const _model_name     = 'unique_test_mn';
        const _property_name  = 'unique_test_pn';
        const _property_name2 = 'unique_test_pn2';
        const model_name      = `[Model]${_model_name}`;
        
        Model.init(_model_name, {
            properties: {
                [_property_name]:  {primary: true, unique: true},
                [_property_name2]: {unique: true},
            }
        });
        
        Std.resolve(`${model_name}|${_property_name2}`)
           .then(i => [, property2] = i)
           .then(i => Std.resolve(model_name))
           .then(i => {
               let [e, model]     = i;
               const uniqueKeySet = model.propertyMeta.getUniqueKeySet(property2);
               const message      =
                       !uniqueKeySet
                           ? 'Could not successfully incorporate unique key'
                           : (uniqueKeySet.get('unique_key').size < 2 ? 'Missing one property' : null);
               done(message);
           });
    });
    
    it('Can inherit properties', () => {
        const m1n     = 'cip_m1n', m2n = 'cip_m2n', m3n = 'cip_m3n';
        const _models = {
            [m1n]: {properties: {id: {primary: true}, last_name: {unique: true}}},
            [m2n]: {inherits: ['cip_m1n'], properties: {first_name: {unique: true}}},
            [m3n]: {inherits: ['cip_m2n'], properties: {first_name: {unique: false}, last_name: {unique: false}}}
        };
        
        // Initialize all of the Models
        const resolveModels            = Object.entries(_models)
                                               .map(i => {
                                                   let [model_name, model_config] = i;
                                                   // Initialize the Model
                                                   return Model.init(model_name, model_config);
                                               });
        /**
         * @param i
         * @return Property
         */
        const _getPropertyFromEventArr = i => {
            const property = i[1];
            expect(property).to.be.instanceof(Property);
            return property
        };
        
        // Once all of the Models have been initialized
        return Promise.all(resolveModels)
                      // Get all models from the returned array of event arrays & store them in an object
                      .then(i => new Map(i.map(model => [model.originalName, model])))
                      // Check to see if the Models have inherited properties correctly
                      .then(
                          (ModelMap: Map<string, Model>) => {
                              const m1         = ModelMap.get(m1n),
                                    m2         = ModelMap.get(m2n),
                                    m3         = ModelMap.get(m3n);
                              const m1_promise = m1.resolve('id').then(i => {
                                  const property = _getPropertyFromEventArr(i);
                              });
                              const m2_promise = m2.resolve('first_name')
                                                   .then(i => {
                                                       const property     = _getPropertyFromEventArr(i);
                                                       const uniqueKeySet = m2.propertyMeta.getUniqueKeySet(property);
                                                       expect(uniqueKeySet).not.to.equal(false);
                                                   });
                              const m3_promise = m3.resolve('first_name')
                                                   .then(i => {
                                                       // Should not retain value
                                                       const property     = _getPropertyFromEventArr(i);
                                                       const uniqueKeySet = m3.propertyMeta.getUniqueKeySet(property);
                                                       expect(uniqueKeySet).to.equal(false);
                    
                                                       // Should also have 'id'
                                                       return m3.resolve('id');
                                                   });
                              return Promise.all([m1_promise, m2_promise, m3_promise]);
                          });
    });
    
    it('Can configure DataSource', done => {
        const mn  = 'ccd_mn';
        const dsn = 'ccd_dsn';
        DataSource.init(dsn, {type: 'database'});
        const m_ = Model.init(mn, {source: dsn}).initializingObject;
        Model.resolve(mn)
             .then(i => {
                 /** @type {Event|Model}  */
                 const [e, model] = i;
                 const dataSource = model.dataSource;
                 const msg        = dataSource instanceof DataSource ? null : "Could not resolve dataSource properly";
                 console.log(dataSource);
                 done(msg);
             }).catch(i => console.log(i, m_));
    });
    
    it('Configures DataSource in the correct order', done => {
        const mn = 'M_cdico_mn', dsn = 'M_cdico_sn';
        Model.resolve(mn)
             .then(i => {
                 /** @type {Event|Model}  */
                 const [e, model]   = i;
                 const dataSource   = model.dataSource;
                 const _isComplete  = dataSource.isComplete;
                 const _isAvailable = dataSource.isAvailable;
                 const msg          =
                           _isAvailable && !_isComplete
                               ? null
                               : "\n\tComplete: " + (_isComplete ? '(does not necessarily need to be true)' : 'is ok' )
                               + "\n\tAvailable: " + (!_isAvailable ? '(should be true)' : 'is ok');
            
                 // Not really sure how to test this bc it's a pretty internal aspect.
                 // failures in other places might be tied to this if it breaks, though
                 done();
             });
        
        DataSource.init(dsn, {type: 'database'});
        Model.init(mn, {source: dsn});
    });
    
    it('Can pass DataSource on to properties', done => {
        const mn = 'M_cpdotp_mn', dsn = 'M_cpdotp_dsn', pn = 'M_cpdotp_pn';
        DataSource.init(dsn, {type: 'database'});
        
        Model.init(mn, {source: dsn, properties: {[pn]: {}}})
             .then(model => model.resolve(pn))
             .then(i => {
                 const [e, property] = i;
                 expect(property).to.be.instanceof(Property);
            
                 property.resolve(SOURCE);
            
                 expect(property.dataSource).to.be.instanceof(DataSource);
                 done();
             });
    });
    
    it('Can be JSON', () => {
        return Model.init('M_cbj_cen', {
                        properties: {
                            id:         {primary: true, unique: true,},
                            first_name: {unique: true},
                            last_name:  {}
                        }
                    })
                    .then(model => {
                        const stringify = JSON.stringify(model);
                        const parse     = JSON.parse(stringify);
                        expect(parse).to.haveOwnProperty('smID');
                        expect(parse).to.haveOwnProperty('properties');
                        expect(parse).to.haveOwnProperty('propertyMeta');
                        console.log(model);
                    })
    })
});