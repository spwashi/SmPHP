/**
 * Created by Sam Washington on 5/20/17.
 */
import {describe, it} from "mocha";
/** @alias {Sm}  */
import {Sm} from "../Sm"

import {expect} from "chai";

describe('_config', () => {
    it('can configure something', () => {
        let initializedModels =
                Sm._config.initialize({
                                          test: {}
                                      },
                                      Sm.entities.Model);
        
        return Promise.all(initializedModels)
                      .then(i => {
                          expect(i[0]).to.be.instanceof(Sm.entities.Model);
                      })
    });
});