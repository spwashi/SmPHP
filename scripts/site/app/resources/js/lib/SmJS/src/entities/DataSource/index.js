import DataSource from "./DataSource";
import DataSourceHaver from "./DataSourceHaver";
import TableDataSource from "./TableDataSource";
import DatabaseDataSource from "./DatabaseDataSource";

export {DataSource, TableDataSource, DatabaseDataSource, DataSourceHaver};
DataSource.registerType(TableDataSource);
DataSource.registerType(DatabaseDataSource);