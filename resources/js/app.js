/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

require('./components/MovieDetail');
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
