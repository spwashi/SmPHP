import {combineReducers} from "redux";
import schemaReducer from "./components/schema/reducer"
import * as actionTypes from './actionTypes'

export default combineReducers({
    
                                   schemas: (state, action) => {
                                       switch (action.type) {
                                           case actionTypes.CREATE_SCHEMA:
                                               const schema = schemaReducer(null, action);
                                               return [
                                                   ...(state || []),
                                                   schema
                                               ];
                                       }
                                       return []
                                   }
                               })