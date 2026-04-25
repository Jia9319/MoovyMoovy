require('./bootstrap');

import React from 'react';
import { createRoot } from 'react-dom/client';
import BookingSelect from './components/BookingSelect';
import BookingSeat from './components/BookingSeat';
import BookingFood from './components/BookingFood';
import BookingPayment from './components/BookingPayment';
import BookingTicket from './components/BookingTicket';

const mountReact = (rootId, Component, initialData) => {
	const rootElement = document.getElementById(rootId);
	if (!rootElement) {
		return;
	}

	const root = createRoot(rootElement);
	root.render(<Component initialData={initialData || {}} />);
};

mountReact('booking-select-root', BookingSelect, window.MoovyBookingSelectData);
mountReact('booking-seat-root', BookingSeat, window.MoovyBookingSeatData);
mountReact('booking-food-root', BookingFood, window.MoovyBookingFoodData);
mountReact('booking-payment-root', BookingPayment, window.MoovyBookingPaymentData);
mountReact('booking-ticket-root', BookingTicket, window.MoovyBookingTicketData);
