import ConfiguredEntity from "../ConfiguredEntity";
import Configuration from "../Configuration";
import {DataSource, SOURCE} from "./DataSource"

/**
 * Mixin to configure the DataSource on
 * @param extended
 * @constructor
 */
const DataSourceHaverConfiguration = (extended: Configuration = Configuration): Configuration =>
    /**
     * @extends Configuration
     */
    class extends extended {
        /**
         * @name DataSourceHaver.configure_dataSource
         * @param source_name
         * @return {Promise<DataSource>}
         */
        configure_dataSource(source_name) {
            /** @this Configuration */
            const dataSourceHaver = this.owner;
            
            if (typeof source_name !== "string") {
                throw new TypeError("Not sure how to handle dataSource configurations that aren't strings");
            }
            
            // Here, it doesn't matter if the DataSource is complete or not since that isn't our primary concern.
            return DataSource.available(source_name)
                             .then(i => {
                                 /** @type {Event|DataSource}  */
                                 const [e, dataSource] = i;
                                 if (!(dataSource instanceof DataSource)) throw new TypeError("Returned DataSource is not of proper type");
                
                                 dataSourceHaver._dataSource = dataSource;
                                 dataSourceHaver.registerAttribute(SOURCE, dataSource);
                
                                 return dataSource;
                             });
        }
        
        /**
         * @alias DataSourceHaver.configure_dataSource
         * @param source_config
         * @return {Promise<DataSource>}
         */
        configure_source(source_config) {
            return this.configure_dataSource(source_config);
        }
        
    };

/**
 * @class DataSourceHaver
 * @name DataSourceHaver
 * @extends ConfiguredEntity
 */
class DataSourceHaver extends ConfiguredEntity {
    /**
     *
     * @return {DataSource}
     */
    get dataSource() {return this._dataSource}
    
    get inheritables() {
        return [...super.inheritables];
    }
}

/**
 *
 * @type {function(Configuration=): {}}
 */
DataSourceHaver.getConfiguration = DataSourceHaverConfiguration;

export default DataSourceHaver;
export {DataSourceHaver};