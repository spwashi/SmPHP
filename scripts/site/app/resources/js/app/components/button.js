import React from 'react'

export class Button extends React.Component {
    handleClick() {
        this.props.callback && this.props.callback(this.props.value);
    }
    
    render() {
        return <button onClick={this.handleClick.bind(this)}>{this.props.text}</button>
    }
}