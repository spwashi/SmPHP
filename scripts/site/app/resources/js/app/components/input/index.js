import React, {Component} from "react";
import PropTypes from "prop-types";

const _bound = [];
const bind   = (fn, self) => {
    const index = _bound.indexOf(fn);
    if (index >= 0) {
        return _bound[index];
    } else {
        fn = fn.bind(self);
        _bound.push(fn);
        return fn;
    }
};

//todo Not entirely sure how to make this appropriately generic... Is there a reason?

export default class Input extends Component {
    _handlers: {}    = {handleChange: null, onBlur: null};
    _inputProperties = {};
    
    constructor(props) {
        super(props);
        this._inputProperties = {...props};
        
        Object.keys(this._handlers)
              .forEach(propName => {
                  if (this._inputProperties[propName]) {
                      this._handlers[propName] = this._inputProperties[propName];
                      delete this._inputProperties[propName]
                  }
              });
        
        this.state = {
            value: this.props.value || '',
        }
    }
    
    onInputFocus(event) {
        let temp           = event.target.value;
        event.target.value = '';
        event.target.value = temp;
    }
    
    handleChange(event) {
        if (this.props.handleChange) {
            return this.props.handleChange(...[...arguments]);
        }
    }
    
    render() {
        const handleChange = (event) => {
            const changeFn = this.handleChange.bind(this, event.target.value);
            this.setState({value: event.target.value || null}, changeFn);
        };
        //This is ugly...
        
        const onFocus = this.onInputFocus.bind(this);
        const onBlur  = (this._handlers.onBlur || (() => {})).bind(this, this.state.value);
        
        const input_props = {
            ...this._inputProperties,
            onBlur:  onBlur,
            onFocus: onFocus
        };
        return (
            <input {...input_props}
                   value={this.state.value || ''}
                   onChange={handleChange} />
        );
    }
}

Input.propTypes = {
    value:        PropTypes.string,
    handleChange: PropTypes.func
};