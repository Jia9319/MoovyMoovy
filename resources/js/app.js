import React from 'react';
import ReactDOM from 'react-dom';

import './bootstrap';

import BookingSelect from './components/BookingSelect';
import BookingSeat from './components/BookingSeat';
import BookingFood from './components/BookingFood';
import BookingPayment from './components/BookingPayment';
import BookingSummary from './components/BookingSummary';
import BookingTicket from './components/BookingTicket';

const mountComponent = (id, Component, initialData) => {
    const element = document.getElementById(id);

    if (!element) {
        return;
    }

    ReactDOM.render(<Component initialData={initialData || {}} />, element);
};

mountComponent('booking-select-root', BookingSelect, window.MoovyBookingSelectData);
mountComponent('booking-seat-root', BookingSeat, window.MoovyBookingSeatData);
mountComponent('booking-food-root', BookingFood, window.MoovyBookingFoodData);
mountComponent('booking-payment-root', BookingPayment, window.MoovyBookingPaymentData);
mountComponent('booking-summary-root', BookingSummary, window.MoovyBookingSummaryData);
mountComponent('booking-ticket-root', BookingTicket, window.MoovyBookingTicketData);
