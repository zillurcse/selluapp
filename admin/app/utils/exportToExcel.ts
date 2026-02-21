import * as XLSX from 'xlsx';

/**
 * Export data to an Excel file
 * @param data Array of objects to export
 * @param fileName Name of the file to download (without extension)
 */
export const exportToExcel = (data: any[], fileName: string) => {
    // specific date object convert to string
    const processedData = data.map(item => {
        const newItem = { ...item };
        for (const key in newItem) {
            if (newItem[key] instanceof Date) {
                newItem[key] = newItem[key].toISOString().split('T')[0];
            }
        }
        return newItem;
    });

    const worksheet = XLSX.utils.json_to_sheet(processedData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
    XLSX.writeFile(workbook, `${fileName}.xlsx`);
};
