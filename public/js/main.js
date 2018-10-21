
$(document).ready(async function () {
		const result = await $.get('api', {method: 'changeDirection', id: 12, direction: 'left' });
		
		console.log(result);
});

