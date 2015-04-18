function draw(sigma, nodes) {
  sigma.graph.addNode({
    // Main attributes:
    id: nodes[0]['id_node'].toString(),
    label: nodes[0]['name'],
    // Display attributes:
    x: 0,
    y: 0,
    size: 2,
    //color: '#f00'
  });
  
  var prev = [], next = [];
  nodes.forEach(function(node) {
    if(node['alpha'] == nodes[0]['id_node'].toString()) {
      next.push(node);
    } else if(node['beta'] == nodes[0]['id_node'].toString()) {
      prev.push(node);
    }
  });
  
  for(var i = 0; i < next.length; ++i) {
    sigma.graph.addNode({
      // Main attributes:
      id: next[i]['id_node'].toString(),
      label: next[i]['name'],
      // Display attributes:
      x: 1,
      y: -(next.length/2)+i+1,
      size: 1,
      color: '#f00'
    }).addEdge({
      id: nodes[0]['id_node'].toString()+'_'+next[i]['id_node'],
      // Reference extremities:
      source: nodes[0]['id_node'].toString(),
      target: next[i]['id_node'].toString()
    });
  }
  
  for(var i = 0; i < prev.length; ++i) {
    sigma.graph.addNode({
      // Main attributes:
      id: prev[i]['id_node'].toString(),
      label: prev[i]['name'],
      // Display attributes:
      x: -1,
      y: -(prev.length/2)+i+1,
      size: 1,
      color: '#f00'
    }).addEdge({
      id: prev[i]['id_node']+'_'+nodes[0]['id_node'].toString(),
      // Reference extremities:
      source: prev[i]['id_node'].toString(),
      target: nodes[0]['id_node'].toString()
    });
  }
  
  sigma.refresh();
}
