import ReactDOM from 'react-dom';

/**
 * Binds react component to Div
 * 
 * @param JSX.element component 
 * @param String id 
 */
export function renderComponentOnDiv(component, id) {
    if (document.getElementById(id)) {
        ReactDOM.render(component, document.getElementById(id));
    }
}
