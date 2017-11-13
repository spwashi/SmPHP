import React from 'react'
import {SmSchema} from "./smSchema"

function makeid(number = 5) {
    let text       = "";
    const possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    
    for (let i = 0; i < number; i++)
        text +=
            possible.charAt(Math.floor(Math.random() * possible.length));
    
    return text;
}

export class SmSchemaContainer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            schemas: []
        };
    }
    
    handleClick(e) {
        this.addSchema(makeid())
    }
    
    addSchema(schema) {
        this.setState((previous, props) => {
            return {
                schemas: [...previous.schemas, schema]
            }
        });
    }
    
    render() {
        let schemaSet = new Set(this.state.schemas);
        const schemas = [];
        
        schemaSet.forEach(schema => {
            schemas.push(<SmSchema key={schema} identity={schema} />);
        });
        
        return <ul onClick={this.handleClick.bind(this)}>{schemas}</ul>
    }
}