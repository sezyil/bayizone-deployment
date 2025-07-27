import { useUserSession } from "../stores/userSession";
import { useSwal } from './useSwal';
//'view', 'create', 'update', 'delete'
export type PermissionType = 'view' | 'create' | 'update' | 'delete';
export type PermissionName =
    | 'attribute'
    | 'attribute_group'
    | 'attribute_value'
    | 'category'
    | 'customer'
    | 'customer_bank_account'
    | 'customer_offer'
    | 'user'
    | 'company_customer'
    | 'company_customer_bank_account'
    | 'company_customer_warehouse'
    | 'option'
    | 'unit'
    | 'product'
    | 'offer_request'
    | 'transaction'
    | 'permission'
    | 'customer_order'
    | 'payment'
    | 'variant'
    ;

export interface IByPermission {
    update: boolean;
    delete: boolean;
    view: boolean;
    create: boolean;
}
const swal = useSwal();

export function useUserPermission() {
    const userSession = useUserSession();
    const permissions = userSession.permissions;
    //split permissions with '-' and get only the second part
    const withoutTypePermissions = permissions.map((p) => p.split('-')[1]);
    function hasPermission(permission: PermissionName | undefined, type?: PermissionType | undefined) {
        if (!permission) {
            return true;
        }
        if (!type) {
            return withoutTypePermissions.includes(permission);
        }
        return permissions.includes(`${type}-${permission}`)
    }

    function hasPermissionByType(type: PermissionType) {
        return (permission: PermissionName) => hasPermission(permission, type);
    }

    const getByName = (permission: PermissionName) => {
        return {
            view: hasPermission(permission, 'view'),
            create: hasPermission(permission, 'create'),
            update: hasPermission(permission, 'update'),
            delete: hasPermission(permission, 'delete'),
        } as IByPermission;
    }

    //get all use getByName
    const getAll = (): { [key in PermissionName]: IByPermission } => {
        return {
            attribute: getByName('attribute'),
            attribute_group: getByName('attribute_group'),
            attribute_value: getByName('attribute_value'),
            category: getByName('category'),
            customer: getByName('customer'),
            customer_bank_account: getByName('customer_bank_account'),
            customer_offer: getByName('customer_offer'),
            user: getByName('user'),
            company_customer: getByName('company_customer'),
            company_customer_bank_account: getByName('company_customer_bank_account'),
            company_customer_warehouse: getByName('company_customer_warehouse'),
            option: getByName('option'),
            unit: getByName('unit'),
            product: getByName('product'),
            permission: getByName('permission'),
            offer_request: getByName('offer_request'),
            transaction: getByName('transaction'),
            customer_order: getByName('customer_order'),
            payment: getByName('payment'),
            variant: getByName('variant'),
        }
    }

    return { hasPermission, hasPermissionByType, getByName, getAll };
}