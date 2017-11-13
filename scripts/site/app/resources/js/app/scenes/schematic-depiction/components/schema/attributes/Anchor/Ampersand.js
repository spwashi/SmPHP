import React, {Component} from "react";
import PropTypes from "prop-types"
import Anchor from "./index";

export default class Ampersand extends Component {
    render() {
        let anchor = null;
        
        if (this.props.anchorID) {
            anchor = <Anchor anchorID={this.props.anchorID} />
        }
        
        return (
            <div className="schema--anchor--ampersand" tabIndex={0}>{anchor}</div>
        );
    }
}

Ampersand.propTypes = {
    anchorID: PropTypes.oneOfType([PropTypes.string, PropTypes.number])
};