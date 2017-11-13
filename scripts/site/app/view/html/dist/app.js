/******/
(function (modules) { // webpackBootstrap
    /******/ 	// The module cache
    /******/
    var installedModules = {};
    /******/
    /******/ 	// The require function
    /******/
    function __webpack_require__(moduleId) {
        /******/
        /******/ 		// Check if module is in cache
        /******/
        if (installedModules[moduleId]) {
            /******/
            return installedModules[moduleId].exports;
            /******/
        }
        /******/ 		// Create a new module (and put it into the cache)
        /******/
        var module = installedModules[moduleId] = {
            /******/            i:       moduleId,
            /******/            l:       false,
            /******/            exports: {}
            /******/
        };
        /******/
        /******/ 		// Execute the module function
        /******/
        modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
        /******/
        /******/ 		// Flag the module as loaded
        /******/
        module.l = true;
        /******/
        /******/ 		// Return the exports of the module
        /******/
        return module.exports;
        /******/
    }
    
    /******/
    /******/
    /******/ 	// expose the modules object (__webpack_modules__)
    /******/
    __webpack_require__.m = modules;
    /******/
    /******/ 	// expose the module cache
    /******/
    __webpack_require__.c = installedModules;
    /******/
    /******/ 	// define getter function for harmony exports
    /******/
    __webpack_require__.d = function (exports, name, getter) {
        /******/
        if (!__webpack_require__.o(exports, name)) {
            /******/
            Object.defineProperty(exports, name, {
                /******/                configurable: false,
                /******/                enumerable:   true,
                /******/                get:          getter
                /******/
            });
            /******/
        }
        /******/
    };
    /******/
    /******/ 	// getDefaultExport function for compatibility with non-harmony modules
    /******/
    __webpack_require__.n = function (module) {
        /******/
        var getter = module && module.__esModule ? /******/            function getDefault() { return module['default']; } : /******/            function getModuleExports() { return module; };
        /******/
        __webpack_require__.d(getter, 'a', getter);
        /******/
        return getter;
        /******/
    };
    /******/
    /******/ 	// Object.prototype.hasOwnProperty.call
    /******/
    __webpack_require__.o = function (object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
    /******/
    /******/ 	// __webpack_public_path__
    /******/
    __webpack_require__.p = "";
    /******/
    /******/ 	// Load entry module and return exports
    /******/
    return __webpack_require__(__webpack_require__.s = 2);
    /******/
})
/************************************************************************/
/******/([
             /* 0 */
             /***/ (function (module, exports) {

// shim for using process in browser
        var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.
        
        var cachedSetTimeout;
        var cachedClearTimeout;
        
        function defaultSetTimout() {
            throw new Error('setTimeout has not been defined');
        }
        
        function defaultClearTimeout() {
            throw new Error('clearTimeout has not been defined');
        }
        
        (function () {
            try {
                if (typeof setTimeout === 'function') {
                    cachedSetTimeout = setTimeout;
                } else {
                    cachedSetTimeout = defaultSetTimout;
                }
            } catch (e) {
                cachedSetTimeout = defaultSetTimout;
            }
            try {
                if (typeof clearTimeout === 'function') {
                    cachedClearTimeout = clearTimeout;
                } else {
                    cachedClearTimeout = defaultClearTimeout;
                }
            } catch (e) {
                cachedClearTimeout = defaultClearTimeout;
            }
        }());
        
        function runTimeout(fun) {
            if (cachedSetTimeout === setTimeout) {
                //normal enviroments in sane situations
                return setTimeout(fun, 0);
            }
            // if setTimeout wasn't available but was latter defined
            if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
                cachedSetTimeout = setTimeout;
                return setTimeout(fun, 0);
            }
            try {
                // when when somebody has screwed with setTimeout but no I.E. maddness
                return cachedSetTimeout(fun, 0);
            } catch (e) {
                try {
                    // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
                    return cachedSetTimeout.call(null, fun, 0);
                } catch (e) {
                    // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
                    return cachedSetTimeout.call(this, fun, 0);
                }
            }
            
        }
        
        function runClearTimeout(marker) {
            if (cachedClearTimeout === clearTimeout) {
                //normal enviroments in sane situations
                return clearTimeout(marker);
            }
            // if clearTimeout wasn't available but was latter defined
            if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
                cachedClearTimeout = clearTimeout;
                return clearTimeout(marker);
            }
            try {
                // when when somebody has screwed with setTimeout but no I.E. maddness
                return cachedClearTimeout(marker);
            } catch (e) {
                try {
                    // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
                    return cachedClearTimeout.call(null, marker);
                } catch (e) {
                    // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
                    // Some versions of I.E. have different rules for clearTimeout vs setTimeout
                    return cachedClearTimeout.call(this, marker);
                }
            }
            
        }
        
        var queue      = [];
        var draining   = false;
        var currentQueue;
        var queueIndex = -1;
        
        function cleanUpNextTick() {
            if (!draining || !currentQueue) {
                return;
            }
            draining = false;
            if (currentQueue.length) {
                queue = currentQueue.concat(queue);
            } else {
                queueIndex = -1;
            }
            if (queue.length) {
                drainQueue();
            }
        }
        
        function drainQueue() {
            if (draining) {
                return;
            }
            var timeout = runTimeout(cleanUpNextTick);
            draining    = true;
            
            var len = queue.length;
            while (len) {
                currentQueue = queue;
                queue        = [];
                while (++queueIndex < len) {
                    if (currentQueue) {
                        currentQueue[queueIndex].run();
                    }
                }
                queueIndex = -1;
                len        = queue.length;
            }
            currentQueue = null;
            draining     = false;
            runClearTimeout(timeout);
        }
        
        process.nextTick = function (fun) {
            var args = new Array(arguments.length - 1);
            if (arguments.length > 1) {
                for (var i = 1; i < arguments.length; i++) {
                    args[i - 1] = arguments[i];
                }
            }
            queue.push(new Item(fun, args));
            if (queue.length === 1 && !draining) {
                runTimeout(drainQueue);
            }
        };

// v8 likes predictible objects
        function Item(fun, array) {
            this.fun   = fun;
            this.array = array;
        }
        
        Item.prototype.run = function () {
            this.fun.apply(null, this.array);
        };
        process.title      = 'browser';
        process.browser    = true;
        process.env        = {};
        process.argv       = [];
        process.version    = ''; // empty string to avoid regexp issues
        process.versions   = {};
        
        function noop() {}
        
        process.on                  = noop;
        process.addListener         = noop;
        process.once                = noop;
        process.off                 = noop;
        process.removeListener      = noop;
        process.removeAllListeners  = noop;
        process.emit                = noop;
        process.prependListener     = noop;
        process.prependOnceListener = noop;
        
        process.listeners = function (name) { return [] };
        
        process.binding = function (name) {
            throw new Error('process.binding is not supported');
        };
        
        process.cwd   = function () { return '/' };
        process.chdir = function (dir) {
            throw new Error('process.chdir is not supported');
        };
        process.umask = function () { return 0; };
        
        /***/
    }),
             /* 1 */
             /***/ (function (module, exports, __webpack_require__) {
        
        /* WEBPACK VAR INJECTION */
        (function (process) {// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

// resolves . and .. elements in a path array with directory names there
// must be no slashes, empty elements, or device names (c:\) in the array
// (so also no leading and trailing slashes - it does not distinguish
// relative and absolute paths)
            function normalizeArray(parts, allowAboveRoot) {
                // if the path tries to go above the root, `up` ends up > 0
                var up = 0;
                for (var i = parts.length - 1; i >= 0; i--) {
                    var last = parts[i];
                    if (last === '.') {
                        parts.splice(i, 1);
                    } else if (last === '..') {
                        parts.splice(i, 1);
                        up++;
                    } else if (up) {
                        parts.splice(i, 1);
                        up--;
                    }
                }
                
                // if the path is allowed to go above the root, restore leading ..s
                if (allowAboveRoot) {
                    for (; up--; up) {
                        parts.unshift('..');
                    }
                }
                
                return parts;
            }

// Split a filename into [root, dir, basename, ext], unix version
// 'root' is just a slash, or nothing.
            var splitPathRe =
                    /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/;
            var splitPath   = function (filename) {
                return splitPathRe.exec(filename).slice(1);
            };

// path.resolve([from ...], to)
// posix version
            exports.resolve = function () {
                var resolvedPath     = '',
                    resolvedAbsolute = false;
                
                for (var i = arguments.length - 1; i >= -1 && !resolvedAbsolute; i--) {
                    var path = (i >= 0) ? arguments[i] : process.cwd();
                    
                    // Skip empty and invalid entries
                    if (typeof path !== 'string') {
                        throw new TypeError('Arguments to path.resolve must be strings');
                    } else if (!path) {
                        continue;
                    }
                    
                    resolvedPath     = path + '/' + resolvedPath;
                    resolvedAbsolute = path.charAt(0) === '/';
                }
                
                // At this point the path should be resolved to a full absolute path, but
                // handle relative paths to be safe (might happen when process.cwd() fails)
                
                // Normalize the path
                resolvedPath = normalizeArray(filter(resolvedPath.split('/'), function (p) {
                    return !!p;
                }), !resolvedAbsolute).join('/');
                
                return ((resolvedAbsolute ? '/' : '') + resolvedPath) || '.';
            };

// path.normalize(path)
// posix version
            exports.normalize = function (path) {
                var isAbsolute    = exports.isAbsolute(path),
                    trailingSlash = substr(path, -1) === '/';
                
                // Normalize the path
                path = normalizeArray(filter(path.split('/'), function (p) {
                    return !!p;
                }), !isAbsolute).join('/');
                
                if (!path && !isAbsolute) {
                    path = '.';
                }
                if (path && trailingSlash) {
                    path += '/';
                }
                
                return (isAbsolute ? '/' : '') + path;
            };

// posix version
            exports.isAbsolute = function (path) {
                return path.charAt(0) === '/';
            };

// posix version
            exports.join = function () {
                var paths = Array.prototype.slice.call(arguments, 0);
                return exports.normalize(filter(paths, function (p, index) {
                    if (typeof p !== 'string') {
                        throw new TypeError('Arguments to path.join must be strings');
                    }
                    return p;
                }).join('/'));
            };

// path.relative(from, to)
// posix version
            exports.relative = function (from, to) {
                from = exports.resolve(from).substr(1);
                to   = exports.resolve(to).substr(1);
                
                function trim(arr) {
                    var start = 0;
                    for (; start < arr.length; start++) {
                        if (arr[start] !== '') break;
                    }
                    
                    var end = arr.length - 1;
                    for (; end >= 0; end--) {
                        if (arr[end] !== '') break;
                    }
                    
                    if (start > end) return [];
                    return arr.slice(start, end - start + 1);
                }
                
                var fromParts = trim(from.split('/'));
                var toParts   = trim(to.split('/'));
                
                var length          = Math.min(fromParts.length, toParts.length);
                var samePartsLength = length;
                for (var i = 0; i < length; i++) {
                    if (fromParts[i] !== toParts[i]) {
                        samePartsLength = i;
                        break;
                    }
                }
                
                var outputParts = [];
                for (var i = samePartsLength; i < fromParts.length; i++) {
                    outputParts.push('..');
                }
                
                outputParts = outputParts.concat(toParts.slice(samePartsLength));
                
                return outputParts.join('/');
            };
            
            exports.sep       = '/';
            exports.delimiter = ':';
            
            exports.dirname = function (path) {
                var result = splitPath(path),
                    root   = result[0],
                    dir    = result[1];
                
                if (!root && !dir) {
                    // No dirname whatsoever
                    return '.';
                }
                
                if (dir) {
                    // It has a dirname, strip trailing slash
                    dir = dir.substr(0, dir.length - 1);
                }
                
                return root + dir;
            };
            
            exports.basename = function (path, ext) {
                var f = splitPath(path)[2];
                // TODO: make this comparison case-insensitive on windows?
                if (ext && f.substr(-1 * ext.length) === ext) {
                    f = f.substr(0, f.length - ext.length);
                }
                return f;
            };
            
            exports.extname = function (path) {
                return splitPath(path)[3];
            };
            
            function filter(xs, f) {
                if (xs.filter) return xs.filter(f);
                var res = [];
                for (var i = 0; i < xs.length; i++) {
                    if (f(xs[i], i, xs)) res.push(xs[i]);
                }
                return res;
            }

// String.prototype.substr - negative index don't work in IE8
            var substr = 'ab'.substr(-1) === 'b'
                ? function (str, start, len) { return str.substr(start, len) }
                : function (str, start, len) {
                    if (start < 0) start = str.length + start;
                    return str.substr(start, len);
                }
            ;
            
            /* WEBPACK VAR INJECTION */
        }.call(exports, __webpack_require__(0)))
        
        /***/
    }),
             /* 2 */
             /***/ (function (module, exports, __webpack_require__) {
        
        "use strict";
        
        Object.defineProperty(exports, "__esModule", {
            value: true
        });
        
        var _get = function get (object, property, receiver) {
            if (object === null) object = Function.prototype;
            var desc = Object.getOwnPropertyDescriptor(object, property);
            if (desc === undefined) {
                var parent = Object.getPrototypeOf(object);
                if (parent === null) { return undefined; } else { return get(parent, property, receiver); }
            } else if ("value" in desc) { return desc.value; } else {
                var getter = desc.get;
                if (getter === undefined) { return undefined; }
                return getter.call(receiver);
            }
        };
        
        var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol"
            ? function (obj) { return typeof obj; }
            : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };
        
        var _createClass = function () {
            function defineProperties(target, props) {
                for (var i = 0; i < props.length; i++) {
                    var descriptor          = props[i];
                    descriptor.enumerable   = descriptor.enumerable || false;
                    descriptor.configurable = true;
                    if ("value" in descriptor) descriptor.writable = true;
                    Object.defineProperty(target, descriptor.key, descriptor);
                }
            }
            
            return function (Constructor, protoProps, staticProps) {
                if (protoProps) defineProperties(Constructor.prototype, protoProps);
                if (staticProps) defineProperties(Constructor, staticProps);
                return Constructor;
            };
        }();
        
        function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
        
        function _possibleConstructorReturn(self, call) {
            if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); }
            return call && (typeof call === "object" || typeof call === "function") ? call : self;
        }
        
        function _inherits(subClass, superClass) {
            if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); }
            subClass.prototype = Object.create(superClass && superClass.prototype, {constructor: {value: subClass, enumerable: false, writable: true, configurable: true}});
            if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
        }
        
        var mkdirp            = __webpack_require__(3);
        var path              = __webpack_require__(1);
        var fs                = __webpack_require__(!(function webpackMissingModule() {
            var e  = new Error("Cannot find module \"fs\"");
            e.code = 'MODULE_NOT_FOUND';
            throw e;
        }()));
        var stripJsonComments = __webpack_require__(4);
        
        var LEADING_TRAILING_SLASH_REGEX = /\/$/g;
        
        function initApp(dirName, Sm) {
            var appPath  = path.resolve(dirName, '..', '..');
            var config_1 = {
                appPath:    appPath,
                configPath: appPath + '/config'
            };
            
            var AppConfiguration = function (_Sm$entities$Configur) {
                _inherits(AppConfiguration, _Sm$entities$Configur);
                
                function AppConfiguration() {
                    _classCallCheck(this, AppConfiguration);
                    
                    return _possibleConstructorReturn(this, (AppConfiguration.__proto__ || Object.getPrototypeOf(AppConfiguration)).apply(this, arguments));
                }
                
                _createClass(AppConfiguration, [{
                    key:   'configure_name',
                    value: function configure_name(name) {
                        this.owner._name = name;
                        return Promise.resolve(name);
                    }
                }, {
                    key:   'configure_namespace',
                    value: function configure_namespace(namespace) {
                        this.owner._namespace = namespace;
                        return Promise.resolve(namespace);
                    }
                }, {
                    key:   'configure_paths',
                    value: function configure_paths(paths) {
                        if ((typeof paths === 'undefined' ? 'undefined' : _typeof(paths)) !== "object") {
                            return Promise.reject("Cannot configure non-object paths");
                        }
                        
                        paths = paths || {};
                        
                        for (var pathIndex in paths) {
                            if (!paths.hasOwnProperty(pathIndex)) continue;
                            this.owner.paths[pathIndex] =
                                paths[pathIndex].replace('CONFIG_PATH', this.owner.paths.config.replace(LEADING_TRAILING_SLASH_REGEX, '')).replace('APP_PATH', this.owner.paths.app.replace(LEADING_TRAILING_SLASH_REGEX, '')).replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
                        }
                        
                        return Promise.resolve(paths);
                    }
                }, {
                    key:   'configure_appPath',
                    value: function configure_appPath(appPath) {
                        this.owner.paths.app = appPath.replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
                        return Promise.resolve(appPath);
                    }
                }, {
                    key:   'configure_configPath',
                    value: function configure_configPath(configPath) {
                        this.owner.paths.config = configPath.replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
                        return Promise.resolve(configPath);
                    }
                }, {
                    key:   'configure_controller',
                    value: function configure_controller(controllerObj) {
                        controllerObj           = (typeof controllerObj === 'undefined' ? 'undefined' : _typeof(controllerObj)) === "object" && controllerObj ? controllerObj : {};
                        controllerObj.namespace = controllerObj.namespace || "Controller";
                        this.owner._controller  = controllerObj;
                        return Promise.resolve(controllerObj);
                    }
                }]);
                
                return AppConfiguration;
            }(Sm.entities.ConfiguredEntity.Configuration);
            
            var createFileAndDirectory = function createFileAndDirectory(text, dirname, endFileName) {
                var createFile = function createFile(filename) {
                    fs.writeFile(filename, text, {flag: 'w'}, function (error) {
                        console.log(error);
                    });
                };
                
                mkdirp(dirname, function (err) {
                    if (err) {
                        console.error(err);
                        return;
                    }
                    createFile(endFileName);
                });
            };
            
            var Application = function (_Sm$entities$Configur2) {
                _inherits(Application, _Sm$entities$Configur2);
                
                function Application() {
                    var _ref;
                    
                    var _temp, _this2, _ret;
                    
                    _classCallCheck(this, Application);
                    
                    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
                        args[_key] = arguments[_key];
                    }
                    
                    return _ret =
                        (_temp =
                            (_this2 =
                                _possibleConstructorReturn(this, (_ref = Application.__proto__ || Object.getPrototypeOf(Application)).call.apply(_ref, [this].concat(args))), _this2), _this2.paths =
                            {}, _temp), _possibleConstructorReturn(_this2, _ret);
                }
                
                _createClass(Application, [{
                    key:   'configure',
                    value: function configure(config) {
                        var _this3 = this;
                        
                        if (typeof config !== 'string') {
                            return _get(Application.prototype.__proto__ || Object.getPrototypeOf(Application.prototype), 'configure', this).call(this, config);
                        }
                        
                        if (config.split('.').reverse().shift() === 'json') {
                            var json_config_file_name = config;
                            var _fs                   = __webpack_require__(!(function webpackMissingModule() {
                                var e  = new Error("Cannot find module \"fs\"");
                                e.code = 'MODULE_NOT_FOUND';
                                throw e;
                            }()));
                            
                            return new Promise(function (resolve, error) {
                                _fs.readFile(json_config_file_name, 'utf8', function (err, text) {
                                    var configuration = JSON.parse(stripJsonComments(text));
                                    
                                    resolve(_this3.configure(configuration));
                                });
                            });
                        }
                    }
                }, {
                    key:   'createConfigRequireFile',
                    value: function createConfigRequireFile() {
                        var _this4 = this;
                        
                        var configPath      = this.paths.config;
                        var requireFileName = configPath + 'index.js';
                        
                        var directories_to_check = new Set(['models', 'routes', 'sources']);
                        
                        var resolvedPaths = [];
                        var lines         = [];
                        
                        directories_to_check.forEach(function (index) {
                            var resolve = void 0,
                                reject  = void 0;
                            var P       = new Promise(function (res, rej) {
                                resolve = res;
                                reject  = rej;
                            });
                            resolvedPaths.push(P);
                            
                            var path     = _this4.paths[index];
                            var filename = path + index + '.js';
                            
                            fs.exists(filename, function (exists) {
                                if (!exists) {
                                    resolve();
                                    return;
                                }
                                lines.push('export {default as ' + index + '} from \'./' + index + '/' + index + '\';');
                                resolve();
                            });
                        });
                        
                        return Promise.all(resolvedPaths).then(function (i) {
                            var resolve = void 0,
                                reject  = void 0;
                            
                            var P = new Promise(function (res, rej) {
                                resolve = res;
                                reject  = rej;
                            });
                            
                            fs.writeFile(requireFileName, lines.join('\n'), function (error) {
                                if (error) {
                                    reject(error);
                                    return;
                                }
                                
                                resolve(true);
                            });
                            
                            return P;
                        });
                    }
                }, {
                    key:   'storeEntityConfig',
                    value: function storeEntityConfig(configuration) {
                        var dir_name    = this.paths.config + '_generated';
                        var file        = 'data.json';
                        var endFileName = dir_name + '/' + file;
                        
                        createFileAndDirectory(JSON.stringify(configuration), dir_name, endFileName);
                    }
                }, {
                    key:   'toJSON__controller',
                    value: function toJSON__controller() {
                        return Object.assign({}, this._controller, {namespace: this.namespace + this._controller.namespace + '\\'});
                    }
                }, {
                    key:   'saveConfig',
                    value: function saveConfig() {
                        var dir_name    = this.paths.config + '_generated';
                        var file        = '_config.json';
                        var endFileName = dir_name + '/' + file;
                        
                        createFileAndDirectory(JSON.stringify(this), dir_name, endFileName);
                    }
                }, {
                    key: 'jsonFields',
                    get: function get () {
                        return new Set(['name', 'namespace', 'controller']);
                    }
                }, {
                    key: 'namespace',
                    get: function get () {
                        return this._namespace + '\\' || '\\';
                    }
                }]);
                
                return Application;
            }(Sm.entities.ConfiguredEntity);
            
            Application.Configuration = AppConfiguration;
            
            var app = new Application();
            
            var configProcesses = [app.configure(config_1), app.configure(config_1.configPath + '/base.json')];
            
            return Promise.all(configProcesses).then(function (i) {
                return app;
            });
        }

/////////////////////////////////////////////////////////////
        
        var app_loader = exports.app_loader = {
            // Establish the paths of the app
            setBase: function setBase(dirName, Sm) {
                return initApp(dirName, Sm);
            }
        };
        
        /***/
    }),
             /* 3 */
             /***/ (function (module, exports, __webpack_require__) {
        
        /* WEBPACK VAR INJECTION */
        (function (process) {
            var path  = __webpack_require__(1);
            var fs    = __webpack_require__(!(function webpackMissingModule() {
                var e  = new Error("Cannot find module \"fs\"");
                e.code = 'MODULE_NOT_FOUND';
                throw e;
            }()));
            var _0777 = parseInt('0777', 8);
            
            module.exports = mkdirP.mkdirp = mkdirP.mkdirP = mkdirP;
            
            function mkdirP(p, opts, f, made) {
                if (typeof opts === 'function') {
                    f    = opts;
                    opts = {};
                }
                else if (!opts || typeof opts !== 'object') {
                    opts = {mode: opts};
                }
                
                var mode = opts.mode;
                var xfs  = opts.fs || fs;
                
                if (mode === undefined) {
                    mode = _0777 & (~process.umask());
                }
                if (!made) made = null;
                
                var cb = f || function () {};
                p      = path.resolve(p);
                
                xfs.mkdir(p, mode, function (er) {
                    if (!er) {
                        made = made || p;
                        return cb(null, made);
                    }
                    switch (er.code) {
                        case 'ENOENT':
                            mkdirP(path.dirname(p), opts, function (er, made) {
                                if (er) cb(er, made);
                                else mkdirP(p, opts, cb, made);
                            });
                            break;
                        
                        // In the case of any other error, just see if there's a dir
                        // there already.  If so, then hooray!  If not, then something
                        // is borked.
                        default:
                            xfs.stat(p, function (er2, stat) {
                                // if the stat fails, then that's super weird.
                                // let the original error be the failure reason.
                                if (er2 || !stat.isDirectory()) cb(er, made);
                                else cb(null, made);
                            });
                            break;
                    }
                });
            }
            
            mkdirP.sync = function sync(p, opts, made) {
                if (!opts || typeof opts !== 'object') {
                    opts = {mode: opts};
                }
                
                var mode = opts.mode;
                var xfs  = opts.fs || fs;
                
                if (mode === undefined) {
                    mode = _0777 & (~process.umask());
                }
                if (!made) made = null;
                
                p = path.resolve(p);
                
                try {
                    xfs.mkdirSync(p, mode);
                    made = made || p;
                }
                catch (err0) {
                    switch (err0.code) {
                        case 'ENOENT' :
                            made = sync(path.dirname(p), opts, made);
                            sync(p, opts, made);
                            break;
                        
                        // In the case of any other error, just see if there's a dir
                        // there already.  If so, then hooray!  If not, then something
                        // is borked.
                        default:
                            var stat;
                            try {
                                stat = xfs.statSync(p);
                            }
                            catch (err1) {
                                throw err0;
                            }
                            if (!stat.isDirectory()) throw err0;
                            break;
                    }
                }
                
                return made;
            };
            
            /* WEBPACK VAR INJECTION */
        }.call(exports, __webpack_require__(0)))
        
        /***/
    }),
             /* 4 */
             /***/ (function (module, exports, __webpack_require__) {
        
        "use strict";
        
        var singleComment = 1;
        var multiComment  = 2;
        
        function stripWithoutWhitespace() {
            return '';
        }
        
        function stripWithWhitespace(str, start, end) {
            return str.slice(start, end).replace(/\S/g, ' ');
        }
        
        module.exports = function (str, opts) {
            opts = opts || {};
            
            var currentChar;
            var nextChar;
            var insideString  = false;
            var insideComment = false;
            var offset        = 0;
            var ret           = '';
            var strip         = opts.whitespace === false ? stripWithoutWhitespace : stripWithWhitespace;
            
            for (var i = 0; i < str.length; i++) {
                currentChar = str[i];
                nextChar    = str[i + 1];
                
                if (!insideComment && currentChar === '"') {
                    var escaped = str[i - 1] === '\\' && str[i - 2] !== '\\';
                    if (!escaped) {
                        insideString = !insideString;
                    }
                }
                
                if (insideString) {
                    continue;
                }
                
                if (!insideComment && currentChar + nextChar === '//') {
                    ret += str.slice(offset, i);
                    offset        = i;
                    insideComment = singleComment;
                    i++;
                } else if (insideComment === singleComment && currentChar + nextChar === '\r\n') {
                    i++;
                    insideComment = false;
                    ret += strip(str, offset, i);
                    offset        = i;
                    
                } else if (insideComment === singleComment && currentChar === '\n') {
                    insideComment = false;
                    ret += strip(str, offset, i);
                    offset        = i;
                } else if (!insideComment && currentChar + nextChar === '/*') {
                    ret += str.slice(offset, i);
                    offset        = i;
                    insideComment = multiComment;
                    i++;
                    
                } else if (insideComment === multiComment && currentChar + nextChar === '*/') {
                    i++;
                    insideComment = false;
                    ret += strip(str, offset, i + 1);
                    offset        = i + 1;
                    
                }
            }
            
            return ret + (insideComment ? strip(str.substr(offset)) : str.substr(offset));
        };
        
        /***/
    })
             /******/]);
//# sourceMappingURL=app.js.map