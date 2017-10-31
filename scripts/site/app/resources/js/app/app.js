const path              = require('path');
const fs                = require('fs');
const stripJsonComments = require('strip-json-comments');

let LEADING_TRAILING_SLASH_REGEX = /\/$/g;

function initApp(dirName, Sm) {
    let appPath    = path.resolve(dirName, '..', '..');
    const config_1 = {
        appPath:    appPath,
        configPath: appPath + '/config'
    };
    
    class AppConfiguration extends Sm.entities.ConfiguredEntity.Configuration {
        owner: Application;
        
        configure_name(name) {
            this.owner._name = name;
            return Promise.resolve(name);
        }
        
        configure_paths(paths) {
            if (typeof paths !== "object") {
                return Promise.reject("Cannot configure non-object paths");
            }
            
            paths = paths || {};
            
            for (let pathIndex in paths) {
                if (!paths.hasOwnProperty(pathIndex)) continue;
                this.owner.paths[pathIndex] = paths[pathIndex].replace('CONFIG_PATH', this.owner.paths.config.replace(LEADING_TRAILING_SLASH_REGEX, ''))
                                                              .replace('APP_PATH', this.owner.paths.app.replace(LEADING_TRAILING_SLASH_REGEX, ''))
                                                              .replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
            }
            
            return Promise.resolve(paths);
        }
        
        configure_appPath(appPath) {
            this.owner.paths.app = appPath.replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
            return Promise.resolve(appPath);
        }
        
        configure_configPath(configPath) {
            this.owner.paths.config = configPath.replace(LEADING_TRAILING_SLASH_REGEX, '') + '/';
            return Promise.resolve(configPath);
        }
    }
    
    class Application extends Sm.entities.ConfiguredEntity {
        static Configuration = AppConfiguration;
               _name;
               paths: {
                   app: string,
                   config: string,
                   models: string,
                   routes: string
               }             = {};
        
        configure(config: Sm.entities.ConfiguredEntity._config) {
            if (typeof config !== 'string') {
                return super.configure(config);
            }
            
            if (config.split('.').reverse().shift() === 'json') {
                const json_config_file_name = config;
                const fs                    = require('fs');
                
                return new Promise((resolve, error) => {
                    fs.readFile(json_config_file_name,
                                'utf8',
                                (err, text) => {
                                    let configuration = JSON.parse(stripJsonComments(text));
                        
                                    resolve(this.configure(configuration));
                                })
                });
                
            }
        }
        
        createConfigRequireFile() {
            const configPath      = this.paths.config;
            const requireFileName = configPath + 'index.js';
            
            const directories_to_check = new Set(['models',
                                                  'routes',
                                                  'sources']);
            
            const resolvedPaths = [];
            const lines         = [];
            
            directories_to_check.forEach(index => {
                let resolve, reject;
                let P = new Promise((res, rej) => {[resolve, reject] = [res, rej];});
                resolvedPaths.push(P);
                
                const path     = this.paths[index];
                const filename = path + (index) + '.js';
                
                fs.exists(filename,
                          exists => {
                              if (!exists) {
                                  resolve();
                                  return;
                              }
                              lines.push(`export {default as ${index}} from './${index}/${index}';`);
                              resolve();
                          })
            });
            
            return Promise.all(resolvedPaths)
                          .then(i => {
                              let resolve, reject;
                
                              const P = new Promise((res, rej) => {[resolve, reject] = [res, rej]});
                
                              fs.writeFile(requireFileName, lines.join('\n'), error => {
                                  if (error) {
                                      reject(error);
                                      return;
                                  }
                    
                                  resolve(true);
                              });
                
                              return P;
                          });
        }
    }
    
    const app = new Application;
    
    const configProcesses = [
        app.configure(config_1),
        app.configure(config_1.configPath + '/config.json')
    ];
    
    return Promise.all(configProcesses).then(i => app);
}

/////////////////////////////////////////////////////////////

export const app_loader = {
    // Establish the paths of the app
    setBase: (dirName, Sm: Sm) => {
        return initApp(dirName, Sm);
    }
};