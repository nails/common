'use strict';

import '../sass/nails.scss';

import NailsTheme from './theme';

window.NailsTheme = NailsTheme;

//  Applies any stored colour scheme preference and wires up switchers. With no
//  stored preference this is a no-op, and the framework continues to follow the
//  operating system.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => NailsTheme.init());
} else {
    NailsTheme.init();
}
