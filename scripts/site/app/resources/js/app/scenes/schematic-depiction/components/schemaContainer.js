import {connect} from "react-redux";
import React, {Component} from "react";
import Schema from "./schema";
import schematic_depiction from "..";

export const SchemaContainer = ({schemas, onCreateSchemaClick}) => {
    schemas             = schemas || [];
    onCreateSchemaClick = onCreateSchemaClick || (() => {});
    return (
        <div>
            <button onClick={() => {onCreateSchemaClick()}}>Add Schema</button>
            <div className="schema--container--items">
                {schemas.map(anchorID => <Schema key={anchorID} schemaID={anchorID} />)}
            </div>
        </div>
    );
};

export default SchemaContainer

export const ActiveSchemaContainer = connect(
    state => {
        const appState = state[schematic_depiction.constants.NAME] || {};
        return {
            schemas: appState.schemas
        }
    },
    dispatch => {
        return {
            onCreateSchemaClick: () => {
                dispatch(schematic_depiction.actions.createSchema())
            }
        }
    })(SchemaContainer);
