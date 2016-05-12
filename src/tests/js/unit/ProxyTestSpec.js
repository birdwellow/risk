describe('Proxy', function () {
	
	it('can send data', function () {
		
		var proxy =
				new CommunicationProxy("ws://ran.dom.host:1337/?joinid=abcd1234");
		
		proxy.send('some.event', {
			string: 'data',
			array: ['d', 'a', 't', 'a'],
			object: {
				key: 'value'
			}
		});
		
		expect(WebSocket._lastMessage()).toBe('{"type":"some.event","data":{"string":"data","array":{"0":"d","1":"a","2":"t","3":"a"},"object":{"key":"value"}}}');
		
	});
	
	it('can receive data', function () {
		
		var proxy =
				new CommunicationProxy("ws://ran.dom.host:1337/?joinid=abcd1234");
		
		proxy.send('some.event', {
			string: 'data',
			array: ['d', 'a', 't', 'a'],
			object: {
				key: 'value'
			}
		});
		
		expect(WebSocket._lastMessage())
				.toBe('{"type":"some.event","data":{"string":"data","array":{"0":"d","1":"a","2":"t","3":"a"},"object":{"key":"value"}}}');
		
	});
	
});