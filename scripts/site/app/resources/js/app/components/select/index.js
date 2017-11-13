import React, {Component} from "react";

export default class Select extends Component {
    _element;
    
    static get_option_entry(title, value) {
        return [value, title];
    }
    
    handleChange() {
        console.log(arguments[0]);
        if (this.props.handleChange)
            this.props.handleChange(...[...arguments]);
    }
    
    render() {
        const options          = this.props.options || [];
        let select_one__option = <option key="_">Select one...</option>;
        let option_elements    = [select_one__option];
        
        (new Set(options)).forEach((index, item) => {
            const [value, title] = item;
            option_elements.push(<option value={value} key={value}>{title}</option>)
        });
        
        let handleChange = () => {
            const element = this.getElement();
            return this.handleChange.bind(this, element);
        };
        
        return (
            <select ref={item => this._element = item}
                    onChange={handleChange}>{option_elements}</select>
        );
    }
    
    getElement() {
        return this._element;
    }
}