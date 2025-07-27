import moment from 'moment';
import 'moment/dist/locale/tr'

let useMoment = moment;


export default function () {
    if (!useMoment) {
        useMoment = moment;
    }
    return useMoment;
} 