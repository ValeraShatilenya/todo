export const PER_PAGE: number = 15;

export interface IStatuse {
    title: string;
    description: string;
    backgroundClass: string;
    value: number;
}

export const STATUSES: IStatuse[] = [
    {
        title: "Красный",
        description: "Супер важная задача",
        backgroundClass: "bg-red-600 focus:ring-red-300",
        value: 1,
    },
    {
        title: "Оранжевый",
        description: "Достаточно важная задача",
        backgroundClass: "bg-orange-600 focus:ring-orange-300",
        value: 2
    },
    {
        title: "Жёлтый",
        description: "Средней важности задача",
        backgroundClass: "bg-yellow-600 focus:ring-yellow-300",
        value: 3,
    },
    {
        title: "Зелёный",
        description: "Не столь важная задача",
        backgroundClass: "bg-green-600 focus:ring-green-300",
        value: 4,
    },
];

interface ITaskSort {
    title: string;
    value: string;
}

export const TASK_SORTS: ITaskSort[] = [
    {
        title: "Дате",
        value: "dateTime",
    },
    {
        title: "Статусу",
        value: "status",
    },
];
