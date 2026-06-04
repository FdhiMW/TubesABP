import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
import 'package:latlong2/latlong.dart';

class VenueMapPage extends StatefulWidget {
  const VenueMapPage({super.key});

  @override
  State<VenueMapPage> createState() => _VenueMapPageState();
}

class _VenueMapPageState extends State<VenueMapPage> {
  static const LatLng venueLocation =
      LatLng(-6.30608258364857, 106.95286754972989);

  LatLng? _currentLocation;
  List<LatLng> _routePoints = [];
  bool _showRoute = false;
  bool _loadingRoute = false;
  String? _error;

  Future<void> _toggleRoute() async {
    if (_showRoute) {
      setState(() {
        _showRoute = false;
        _routePoints = [];
        _error = null;
      });
      return;
    }

    setState(() {
      _loadingRoute = true;
      _error = null;
    });

    try {
      LocationPermission permission = await Geolocator.checkPermission();

      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }

      if (permission == LocationPermission.denied) {
        throw 'Izin lokasi ditolak.';
      }

      if (permission == LocationPermission.deniedForever) {
        throw 'Izin lokasi ditolak permanen. Buka pengaturan aplikasi.';
      }

      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      final current = LatLng(position.latitude, position.longitude);

      final uri = Uri.parse(
        'https://router.project-osrm.org/route/v1/driving/'
        '${position.longitude},${position.latitude};'
        '${venueLocation.longitude},${venueLocation.latitude}'
        '?overview=full&geometries=geojson',
      );

      final response = await http.get(uri);

      if (response.statusCode != 200) {
        throw 'Gagal mengambil rute.';
      }

      final data = jsonDecode(response.body) as Map<String, dynamic>;
      final routes = data['routes'] as List<dynamic>;

      if (routes.isEmpty) {
        throw 'Rute tidak ditemukan.';
      }

      final geometry = routes.first['geometry'] as Map<String, dynamic>;
      final coordinates = geometry['coordinates'] as List<dynamic>;

      final points = coordinates.map((item) {
        final coord = item as List<dynamic>;
        final lon = (coord[0] as num).toDouble();
        final lat = (coord[1] as num).toDouble();
        return LatLng(lat, lon);
      }).toList();

      setState(() {
        _currentLocation = current;
        _routePoints = points;
        _showRoute = true;
        _loadingRoute = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _loadingRoute = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Lokasi Venue'),
      ),
      body: Stack(
        children: [
          FlutterMap(
            options: const MapOptions(
              initialCenter: venueLocation,
              initialZoom: 16,
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.example.pendopo_uti_mobile',
              ),
              if (_showRoute && _routePoints.isNotEmpty)
                PolylineLayer(
                  polylines: [
                    Polyline(
                      points: _routePoints,
                      strokeWidth: 5,
                    ),
                  ],
                ),
              MarkerLayer(
                markers: [
                  if (_currentLocation != null)
                    Marker(
                      point: _currentLocation!,
                      width: 48,
                      height: 48,
                      child: const Icon(
                        Icons.my_location,
                        color: Colors.blue,
                        size: 36,
                      ),
                    ),
                  Marker(
                    point: venueLocation,
                    width: 48,
                    height: 48,
                    child: const Icon(
                      Icons.location_pin,
                      color: Colors.red,
                      size: 44,
                    ),
                  ),
                ],
              ),
            ],
          ),

          if (_loadingRoute)
            const Positioned(
              top: 16,
              left: 0,
              right: 0,
              child: Center(
                child: Card(
                  child: Padding(
                    padding: EdgeInsets.all(12),
                    child: Text('Mengambil rute...'),
                  ),
                ),
              ),
            ),

          if (_error != null)
            Positioned(
              top: 16,
              left: 16,
              right: 16,
              child: Card(
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Text(_error!),
                ),
              ),
            ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _loadingRoute ? null : _toggleRoute,
        icon: Icon(_showRoute ? Icons.route : Icons.alt_route),
        label: Text(_showRoute ? 'Hide Route' : 'Route'),
      ),
    );
  }
}