$(function(){ // on dom ready

    $('#daily-graph').cytoscape({
        layout: {
            name: 'cose',
            padding: 10
        },

        style: cytoscape.stylesheet()
            .selector('node')
            .css({
                'shape': 'data(faveShape)',
                'width': 'mapData(weight, 30, 30, 20, 20)',
                'content': 'data(name)',
                'text-valign': 'center',
                'text-outline-width': 2,
                'text-outline-color': 'data(faveColor)',
                'background-color': 'data(faveColor)',
                'color': '#fff'
            })
            .selector(':selected')
            .css({
                'border-width': 3,
                'border-color': '#333'
            })
            .selector('edge')
            .css({
                'curve-style': 'bezier',
                'opacity': 0.666,
                'width': 'mapData(strength, 70, 100, 2, 6)',
                'target-arrow-shape': 'triangle',
                'source-arrow-shape': 'circle',
                'line-color': 'data(faveColor)',
                'source-arrow-color': 'data(faveColor)',
                'target-arrow-color': 'data(faveColor)'
            })
            .selector('edge.questionable')
            .css({
                'line-style': 'dotted',
                'target-arrow-shape': 'diamond'
            })
            .selector('.faded')
            .css({
                'opacity': 0.25,
                'text-opacity': 0
            }),

        elements: {
            nodes: [
                { data: { id: 'j', name: 'Oana', weight: 65, faveColor: '#6FB1FC', faveShape: 'triangle' } },
                { data: { id: 'e', name: 'Alex', weight: 45, faveColor: '#EDA1ED', faveShape: 'ellipse' } },
                { data: { id: 'k', name: 'Laurentiu', weight: 75, faveColor: '#86B342', faveShape: 'octagon' } },
                { data: { id: 'p', name: 'Adrian', weight: 75, faveColor: '#86B342', faveShape: 'rectangle' } },
                { data: { id: 'g', name: 'Stefan', weight: 70, faveColor: '#F5A45D', faveShape: 'rectangle' } }
            ],
            edges: [
                { data: { source: 'j', target: 'e', faveColor: '#6FB1FC', strength: 20 } },
                { data: { source: 'j', target: 'k', faveColor: '#6FB1FC', strength: 20 } },
                { data: { source: 'j', target: 'g', faveColor: '#6FB1FC', strength: 20 } },

                { data: { source: 'e', target: 'j', faveColor: '#EDA1ED', strength: 30 } },
                { data: { source: 'e', target: 'k', faveColor: '#EDA1ED', strength: 30 }, classes: 'questionable' },

                { data: { source: 'k', target: 'j', faveColor: '#86B342', strength: 30 } },
                { data: { source: 'k', target: 'e', faveColor: '#86B342', strength: 30 } },
                { data: { source: 'k', target: 'g', faveColor: '#86B342', strength: 30 } },

                { data: { source: 'g', target: 'j', faveColor: '#F5A45D', strength: 4 } },
                { data: { source: 'e', target: 'p', faveColor: '#F5A45D', strength: 20 } }
            ]
        },

        ready: function(){
            window.cy = this;

            // giddy up
        }
    });

});