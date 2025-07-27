/**
 * Author : Berke GÜLEÇ
 */
/**
 * Represents the available HTTP methods for a datatable request.
 */
type IDatatableMethod = "get" | "post" | "put" | "delete";

/**
 * Represents the options for a datatable.
 */
interface IDatatableOptions {
    /**
     * The filter object used to filter the datatable data.
     */
    filter: object;

    /**
     * The URL to send the datatable request to.
     */
    requestURL: string;

    /**
     * The HTTP method to use for the datatable request.
     */
    method: IDatatableMethod;

    /**
     * Indicates whether the datatable should be refreshed after performing an action.
     */
    refresh: boolean;

    /**
     * The content of the datatable data, represented as an array of datatable columns.
     */
    dataContent: IDataTableColumn[];

    /**
     * The CSS class to apply to the datatable header.
     */
    headClass: string;

    /**
     * The inline CSS styles to apply to the datatable header.
     */
    headStyle: string;

    /**
     * The CSS class to apply to the datatable wrapper element.
     */
    wrapperClass: string;

    /**
     * The inline CSS styles to apply to the datatable wrapper element.
     */
    wrapperStyle: string;

    /**
     * The title of the datatable card.
     */
    cardTitle: string;

    /**
     * The ID of the datatable item.
     */
    itemId: string;
}

/**
 * Represents the possible types for rendering datatable columns.
 */
type RenderType = IDataTableColumnContent | string | number | boolean;


interface IDataTableColumn {
    /**
     * The name of the column. If the key is an object, use the render function to retrieve the value.
     */
    data: string;

    /**
     * The header text of the column.
     */
    headText: string;

    /**
     * Optional. A function that determines how the data should be rendered in the column.
     * It takes the row data, column data, and an optional index as parameters, and returns the rendered output.
     * @param row  The row data.
     * @param data  The column data.
     * @param index  Optional. The index of the row.
     * @returns  The rendered output.
     */
    render?: (row: any, data: any, index?: number) => RenderType;
}

/**
 * Represents the content of a data table column.
 */
interface IDataTableColumnContent {
    /**
     * Indicates whether the column content contains raw HTML content or plain text.
     */
    isRaw: boolean;

    /**
     * An array of column nodes that make up the content of the column.
     */
    nodes: IDataTableColumnNode[];

    /**
     * The inner HTML content of the column.
     * This property is only used if the column content is not raw HTML.
     **/

}

/**
 * Represents a node within a data table column.
 */
export interface IDataTableColumnNode {
    /**
     * Specifies the type of HTML element for the column node.
     */
    elementType: keyof HTMLElementTagNameMap;

    /**
     * Indicates whether the column node contains raw HTML content or plain text.
     */
    isRaw: boolean;


    /**
     * nodeActive
     * Specifies the node is active or not
     * @type {boolean}
     * @memberof IDataTableColumnNode
     **/
    nodeActive?: boolean;

    /**
     * Specifies the CSS class name(s) to be applied to the column node.
     */
    class: string;

    /**
     * title attribute for the column node. 
     */
    title?: string
    /**
     * Represents the trigger configuration for the column node.
     */
    trigger?: IDataTableColumnTrigger;

    /**
     * The inner HTML content of the column node.
     */
    innerHTML: string;

    subNodes?: IDataTableColumnNode[];
}

/**
 * Represents the trigger configuration for a data table column node.
 */
interface IDataTableColumnTrigger {
    /**
     * Specifies the type of event to listen for.
     */
    type: keyof HTMLElementEventMap;

    /**
     * The name of the event to emit.
     */
    emitName: string;

    /**
     * The first parameter to pass when emitting the event.
     */
    firstParam: any;

    /**
     * An optional second parameter to pass when emitting the event.
     */
    secondParam?: any;
}

/**
 * Represents the trigger configuration for a data table cell.
 */
interface IDataTableCellTrigger {
    /**
     * The name of the trigger.
     */
    name: string;

    /**
     * The name of the event to emit.
     */
    emitName: string;

    /**
     * The first parameter to pass when emitting the event.
     */
    firstParam: any;

    /**
     * An optional second parameter to pass when emitting the event.
     */
    secondParam?: any;

    /**
     * The event data.
     */
    event: any;
}

export {
    IDataTableColumn,
    IDatatableOptions,
    IDataTableColumnTrigger,
    IDataTableCellTrigger
}