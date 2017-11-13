import React, {Component} from "react";

export default class Instance extends Component {
    constructor(props) {
        super(props);
        this.state = {};
    }
    
    render() {
        
        const anchor = this.props.anchor;
        
        return (
            <div className="instance schema--instance">{anchor}</div>
        );
    }
}