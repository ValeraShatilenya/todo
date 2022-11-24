import { IFile } from "./fileInterfaces";

export interface IUser {
    id: number;
    name: string;
}

export interface ITask {
    id: number;
    title: string;
    description: string;
    dateTime: string;
    status: number;
    files: Array<IFile>;
    canEdit?: boolean;
    user?: IUser;
    completed_user?: IUser;
}

export interface IData {
    completed: { data: Array<ITask>, page: number, total: number, sort: string};
    notCompleted: { data: Array<ITask>, page: number, total: number, sort: string};
}

export enum Types {
    notCompleted = 'notCompleted',
    completed = 'completed',
};

export interface IMainTaskData {
    data: IData,
    getNotCompleted(): Promise<any>,
    getCompleted(): Promise<any>,
    functionByType: {[Types.notCompleted](): Promise<any>, [Types.completed](): Promise<any>},
    create(take: object): Promise<any>,
    changeCompleted(id: number, type: Types): Promise<any>,
    update(take: object): Promise<any>,
    destroy(id: number, type: Types): Promise<any>,
    sendPdfToMail(): Promise<any>,
}
