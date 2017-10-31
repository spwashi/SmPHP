/** @name {Sm.entities}  */
import Model from "./Model/Model";
import Property from "./Property/Property";
import Datatype from "./Datatype";
import ConfiguredEntity from "./ConfiguredEntity";
import {DatabaseDataSource, DataSource, TableDataSource} from "./DataSource";
// configuration for the frameworkentities

export const entities = {
    ConfiguredEntity,
    Property,
    Model,
    Datatype,
    TableDataSource,
    DatabaseDataSource,
    DataSource
};
export default entities;