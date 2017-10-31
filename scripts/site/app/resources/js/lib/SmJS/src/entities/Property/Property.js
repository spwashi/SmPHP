/**
 * @class Property
 */
import {DataSourceHaver} from "../DataSource/index";
import Datatype from "../Datatype";

/**
 * @extends Sm.entities.DataSource.DataSourceHaver.Configuration
 */
class PropertyConfiguration extends DataSourceHaver.getConfiguration() {
    get inheritables() {
        return [...super.inheritables, 'datatypes'];
    }
    
    /**
     *
     * @param datatype
     * @return {Promise.<Set>}
     */
    configure_datatypes(datatype) {
        const self     = this.owner;
        datatype       = Array.isArray(datatype) ? datatype : [datatype];
        const promises = datatype.filter(i => !!i)
                                 .map(dt => Datatype.resolve(dt)
                                                    .then(event__dt => event__dt[1]));
        return Promise.all(promises)
                      .then(datatypes => self._datatypes = new Set([...datatypes, ...(self._datatypes || [])]))
    }
    
    configure_length(length) {
        this.owner._length = parseInt(length);
    }
}

/**
 * @name Sm.entities.Property
 * @class Sm.entities.Property
 * @extends DataSourceHaver
 * @extends Sm.Std
 */
export default class Property extends DataSourceHaver {
    static Configuration = PropertyConfiguration;
    static smID          = 'Property';
           _datatypes;
    
    /**
     * The Datatypes that this is allowed to be.
     * @return {Set}
     */
    get datatypes() {
        return this._datatypes = this._datatypes || new Set;
    }
    
    get jsonFields() {
        return new Set([...super.jsonFields, 'datatypes', '?length'])
    }
    
    toJSON__length() {
        return this._length = this._length || null;
    }
    
    /**
     * Returns the SmIDs of the Datatypes that this Property can be
     * @return {Array}
     */
    toJSON__datatypes() {
        return [...this.datatypes].map(dt => dt.smID);
    }
}