import React from 'react'
import {Button} from "./button";

export class Title extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            editing: false,
        }
    }
    
    componentWillReceiveProps(nextProps) {
        this.setState({name: nextProps.name});
    }
    
    handleClick(e) {
        this.setState((previous) => {
            if (!previous.editing) {
                return {editing: true};
            }
        });
        e.stopPropagation();
    }
    
    update(doSetValue) {
        Promise.resolve(doSetValue && this.props.update(this.state.name))
               .then(i => this.setState({
                                            editing: false
                                        }));
    }
    
    handleChange(event) {
        this.setState({name: event.target.value})
    };
    
    render() {
        let inside = <span className="schema--name">{this.state.name || '[no name set]'}</span>;
        
        const setEditable = this.update.bind(this);
        
        if (this.state.editing) {
            inside = [
                <input key='title--input' type="text" value={this.state.name || ''} onChange={this.handleChange.bind(this)} />,
                <div key='title--buttons' className="buttons">
                    <Button text="Confirm" callback={setEditable} value={true} />
                    <Button text="Cancel" callback={setEditable} value={false} />
                </div>
            ];
        }
        
        return (
            <div className="action-container" onClick={this.handleClick.bind(this)}>
                {inside}
            </div>)
    }
}