import React, {Component} from "react";
import {KEY_CHAR_CODES} from "../../../../constants";
import Schema from "../..";

export default class Perspective extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isActive: false
        };
    }
    
    get schema() {
        return this.perspectiveSchema = this.perspectiveSchema || <Schema />
    }
    
    handleLabelClick() {
        this.toggleActive();
    }
    
    handleLabelKeypress(event) {
        switch (event.charCode) {
            case KEY_CHAR_CODES.ENTER:
            case KEY_CHAR_CODES.SPACE_BAR:
                this.toggleActive();
                break;
        }
    }
    
    toggleActive() {
        this.setState({isActive: !this.state.isActive})
    }
    
    render() {
        return (
            <div className="perspective schema--attribute schema--attribute-perspective">
                <div className="schema--attribute--label attribute--label"
                     tabIndex={0}
                     onKeyPress={this.handleLabelKeypress.bind(this)}
                     onClick={this.handleLabelClick.bind(this)}></div>
                {this.state.isActive ? this.schema : null}
            </div>
        );
    }
}