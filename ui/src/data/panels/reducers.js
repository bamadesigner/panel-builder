import * as types from './types';

const INITIAL_STATE = {};

const reducer = ( state = INITIAL_STATE, action ) => {
	switch ( action.type ) {
		case types.ADD_PANEL:
		case types.REMOVE_PANEL:
		case types.UPDATE_PANEL:
			return Object.assign(
				{},
				state,
				{ [ action.clientId ]: panel( state[ action.clientId ], action ) },
			);
		default:
			return state;
	}
};

export default reducer;
