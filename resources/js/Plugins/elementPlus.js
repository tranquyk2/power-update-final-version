import 'element-plus/dist/index.css'
import {
    ElButton,
    ElInput,
    ElDatePicker,
    ElSelect,
    ElOption,
    ElTable,
    ElTableColumn,
    ElText,
    ElPagination
} from 'element-plus';

export default {
    install: (app) => {
        app.component([ElPagination.name], ElPagination);
        app.component([ElText.name], ElText);
        app.component([ElButton.name], ElButton);
        app.component([ElInput.name], ElInput);
        app.component([ElDatePicker.name], ElDatePicker);
        app.component([ElSelect.name], ElSelect);
        app.component([ElOption.name], ElOption);
        app.component([ElTable.name], ElTable);
        app.component([ElTableColumn.name], ElTableColumn);
    }
}