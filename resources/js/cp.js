import ActionList from "./components/Actions/ActionList";
import ActionItem from "./components/Actions/ActionItem";
import CommerceCreateForm from "./components/Publish/CommerceCreateForm";
import CustomerOrdersFieldtype from "./components/Fieldtypes/CustomerOrdersFieldtype";
import MoneyFieldtype from "./components/Fieldtypes/MoneyFieldtype";
import LineItemsFieldtype from "./components/Fieldtypes/LineItemsFieldtype";
import OrderStatus from "./components/Settings/OrderStatus";
import ShippingZone from "./components/Settings/ShippingZone";
import TaxRate from "./components/Settings/TaxRate";

Statamic.$components.register('simple-commerce-actions', ActionList);
Statamic.$components.register('simple-commerce-action-item', ActionItem);
Statamic.$components.register('commerce-create-form', CommerceCreateForm);
Statamic.$components.register('customer-orders-fieldtype', CustomerOrdersFieldtype);
Statamic.$components.register('money-fieldtype', MoneyFieldtype);
Statamic.$components.register('line-items-fieldtype', LineItemsFieldtype);
Statamic.$components.register('order-status-settings', OrderStatus);
Statamic.$components.register('shipping-zone-settings', ShippingZone);
Statamic.$components.register('tax-rate-settings', TaxRate);
