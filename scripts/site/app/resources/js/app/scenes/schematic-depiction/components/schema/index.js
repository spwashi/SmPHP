import React, {Component} from "react";
import IntegrationScheme from "./attributes/IntegrationScheme";
import Perspective from "./attributes/Perspective";
import Ampersand from "./attributes/Anchor/Ampersand";

const ApplicationContainer = ({schemaID, integrationSchemeID}) => {
    return (
        <div className="schema--application-container">
            <Ampersand anchorID={schemaID} />
            <IntegrationScheme />
        </div>
    )
};

const Schema = ({schemaID, integrationSchemeID}) => {
    return (
        <div className="schema--schema">
            <header>
                <Perspective />
                <ApplicationContainer schemaID={schemaID} />
            </header>
        </div>
    )
};
export default Schema;