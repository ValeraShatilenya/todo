export interface IGroup {
    id: number;
    name: string;
    canEdit: boolean;
    dateTime: string;
    description: string;
}

export interface IGroups extends Array<IGroup>{}

export interface ITaskGroup {
    id: number;
    name: string;
}

export interface IUser {
    id: number;
    isAdmin: boolean;
    name: string;
    email: string;
};

export interface IUsers extends Array<IUser>{}
