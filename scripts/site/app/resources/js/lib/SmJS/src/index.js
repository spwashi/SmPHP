/// <reference path="/docs/global.d.ts" />
import std from "./std";
import * as _config from "./_config";
import entities from "./entities";
import errors from "./errors";
import util from "./util";

export {_config}
export {std} ;
export {entities} ;
export {errors}
export {util}

export {Sm};
/**
 * @name Sm
 */
const Sm = {_config, std, entities, errors, util};
export default Sm;