import moment from "moment";
import 'moment/dist/locale/tr'

/**
 * Generate months
 * @param onlyName if true, only month names will be returned
 */
export const generateMonths = (onlyName: boolean = false) => {
    moment.locale('tr')
    const locale = moment();
    const months = [];
    for (let i = 0; i < 12; i++) {
        if (onlyName) {
            months.push(locale.month(i).format("MMMM"));
        } else {
            months.push({
                name: locale.month(i).format("MMMM"),
                value: i + 1
            });
        }
    }
    return months;
};