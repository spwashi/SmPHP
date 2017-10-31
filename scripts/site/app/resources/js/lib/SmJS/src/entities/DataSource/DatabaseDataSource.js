import DataSource from "./DataSource";

class DatabaseDataSource extends DataSource {
    static type = 'database';
    
    boonman() {
        return 'grandslam';
    }
}

export default DatabaseDataSource;
export {DatabaseDataSource}