import {extend, localize, ValidationObserver, ValidationProvider} from 'vee-validate';
import * as rules from 'vee-validate/dist/rules';
import Vue from "vue";
import ru from 'vee-validate/dist/locale/ru.json';
import en from 'vee-validate/dist/locale/en.json';

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
Object.keys(rules).forEach(rule => {
	extend(rule, rules[rule]);
});

// with typescript
for (let [rule, validation] of Object.entries(rules)) {
	extend(rule, {
		...validation
	});
}
localize(ru);

localize({
	ru,
	en,
});
