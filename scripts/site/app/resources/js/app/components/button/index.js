import PropTypes from 'prop-types';
import React, {Component} from "react";

const buttonStyle = {
    margin: '10px 10px 10px 0'
};

export default class Button extends React.Component {
    handleClick() {
        if (this.props.handleClick)
            return this.props.handleClick(...arguments);
    }
    
    render() {
        return (
            <button
                onClick={this.handleClick.bind(this)}>{this.props.label}</button>
        );
    }
}

Button.propTypes = {
    handleClick: PropTypes.func,
    label:       PropTypes.string.isRequired
};